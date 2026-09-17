
app.get('/motor-vehicle-loans-info', async (req, res) => {
  try {

    const { start_date, end_date } = req.query;


    if (!start_date || !end_date) {
      return res.status(400).json({
        error: "start_date and end_date are required query params"
      });
    }


    /**
     * Get motor vehicle loans
     */
 const [loans] = await pool.query(
  `
SELECT 
    loans.*,
    users.id AS consultant_id,
    
    CONCAT(users.first_name, ' ', users.last_name) AS consultant_name,

    referrer.id AS referrer_id,
    CONCAT(referrer.first_name, ' ', referrer.last_name) AS referrer_name,

    offices.id AS branch_id,
    offices.name AS branch_name,

    province.name AS province_name

FROM loans

LEFT JOIN users 
    ON loans.loan_officer_id = users.id

LEFT JOIN users AS referrer
    ON loans.referrer = referrer.id

LEFT JOIN offices 
    ON loans.referrer_branch = offices.id

LEFT JOIN province
    ON offices.province_id = province.id

  WHERE loans.loan_product_id = ?
  AND loans.created_at >= ?
  AND loans.created_at < DATE_ADD(?, INTERVAL 1 DAY)

  `,
  [0, start_date, end_date]
);


    const loanIds = loans.map(l => l.id);



    /**
     * Get vehicles attached to loans
     */
   const [vehicles] = await pool.query(
  `
  SELECT 
      vehicles.*

  FROM vehicles

  INNER JOIN loans 
      ON vehicles.loan_id = loans.id

  WHERE loans.loan_product_id = ?
  AND loans.created_at >= ?
  AND loans.created_at < DATE_ADD(?, INTERVAL 1 DAY)

  `,
  [0, start_date, end_date]
);




    /**
     * Get repayment transactions
     */
    const [transactions] = await pool.query(
      `
      SELECT 
          loan_transactions.*

      FROM loan_transactions

      INNER JOIN loans
          ON loan_transactions.loan_id = loans.id

      WHERE loans.loan_product_id = ?
      AND loans.created_at BETWEEN ? AND ?

      AND loan_transactions.transaction_type = 'repayment'

      `,
      [0, start_date, end_date]
    );





    /**
     * Calculate summary
     */
 function calculateSummary(selectedLoans) {

    const selectedLoanIds = selectedLoans.map(l => l.id);

    const selectedVehicles = vehicles.filter(v =>
        selectedLoanIds.includes(v.loan_id)
    );

    const selectedTransactions = transactions.filter(t =>
        selectedLoanIds.includes(t.loan_id)
    );

    let collections = 0;

    selectedTransactions.forEach(t => {

        const credit = Number(t.credit || 0);

        if (t.transaction_type === 'repayment') {

            if (t.payment_apply_to === 'reloan_payment') {

                collections += Number(t.balance_bf || 0);

            } else if (
                ['full_payment', 'part_payment'].includes(t.payment_apply_to)
            ) {

                collections += credit;

            }

        }

    });

    // Calculate expected collections
    const expectedCollections = selectedLoans.reduce((sum, loan) => {
        const principal = Number(loan.principal || 0);
        const interestRate = Number(loan.interest_rate || 0);

        return sum + principal + (principal * (interestRate / 100));
    }, 0);

    const expectedInterest = selectedLoans.reduce((sum, loan) => {
         const principal = Number(loan.principal || 0);
        const interestRate = Number(loan.interest_rate || 0);

    return sum + (principal * (interestRate / 100));
}, 0);

const today = new Date();

const formattedLoans = selectedLoans.map(loan => {

    const dueDate = loan.expected_first_repayment_date
        ? new Date(loan.expected_first_repayment_date)
        : null;

    let daysInDefault = 0;

    if (dueDate && today > dueDate) {
        daysInDefault = Math.floor(
            (today - dueDate) / (1000 * 60 * 60 * 24)
        );
    }

    return {
        ...loan,
        due_date: loan.expected_first_repayment_date,
        days_in_default: daysInDefault
    };
});

    return {

        number_of_loans: selectedLoans.length,

        number_of_vehicles: selectedVehicles.length,

        total_vehicle_value: selectedVehicles.reduce(
            (sum, v) => sum + Number(v.market_value || 0),
            0
        ),

        total_loan_portfolion: selectedLoans.reduce((sum, loan) => {
    const principal = Number(loan.principal || 0);
    const interestRate = Number(loan.interest_rate || 0);

    return sum + principal + (principal * (interestRate / 100));
}, 0),

        expected_interest: expectedInterest,

        expected_collections: expectedCollections,

        total_collections: collections,

        loans_list: formattedLoans,

        vehicles_list: selectedVehicles,

        collections_list: selectedTransactions

    };

}





    /**
     * National level
     */
    const national = calculateSummary(loans);






    /**
     * Province level
     */
    const provinceGroups = {};


    loans.forEach(loan=>{


      const province =
          loan.province_name || "Unknown";


      if(!provinceGroups[province]){
          provinceGroups[province]=[];
      }


      provinceGroups[province].push(loan);


    });



    const provinces =
      Object.keys(provinceGroups)
      .map(province=>({


          province_name:province,


          ...calculateSummary(
              provinceGroups[province]
          ),



          branches:
          getBranches(
              provinceGroups[province]
          )


      }));





    /**
     * Branch grouping
     */
    function getBranches(branchLoans){


      const groups={};



      branchLoans.forEach(loan=>{


        const branch =
          loan.branch_name || "Unknown";



        if(!groups[branch]){
            groups[branch]=[];
        }


        groups[branch].push(loan);


      });



      return Object.keys(groups)
      .map(branch=>({


          branch_name:branch,


          ...calculateSummary(
              groups[branch]
          ),


          consultants:
              getConsultants(
                  groups[branch]
              )


      }));


    }





    /**
     * Consultant grouping
     */
    function getConsultants(consultantLoans){


      const groups={};



      consultantLoans.forEach(loan=>{


        const consultant =
          loan.consultant_name || "Unknown";



        if(!groups[consultant]){
            groups[consultant]=[];
        }


        groups[consultant].push(loan);


      });



      return Object.keys(groups)
      .map(consultant=>({


          consultant_name:consultant,


          ...calculateSummary(
              groups[consultant]
          )


      }));


    }






    return res.json({

      success:true,


      national,


      provinces


    });




  } catch(error){


    console.error(
      "Motor vehicle loans info error:",
      error
    );


    return res.status(500).json({

      error:
      "Failed to fetch motor vehicle loans information"

    });


  }

});