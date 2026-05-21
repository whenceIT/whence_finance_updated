@extends('layouts.master')

@section('title')

Verify Mobile Number

@endsection



@section('content')


<div class="container">


<div

class="box"

style="

max-width:

500px;

margin:

50px auto;

border:

none;

border-radius:

20px;

overflow:

hidden;

box-shadow:

0 20px 50px rgba(
0,
0,
0,
.08
);

">




<div

class="box-header"

style="

background:

linear-gradient(

135deg,

#3c8dbc,

#00c0ef

);

padding:

25px;

color:

white;

text-align:

center;

">

<h2>

Verify Mobile Number

</h2>


<p>

Check who a mobile number is registered under

</p>

</div>




<div

class="box-body"

style="

padding:

35px;

">




<div class="form-group">

<label>

Mobile Number

</label>



<input

type="text"

id="mobile"

class="form-control"

placeholder="097xxxxxxx"

style="

height:

50px;

font-size:

18px;

border-radius:

12px;

">



<small

style="

display:

block;

margin-top:

10px;

font-size:

12px;

color:

#888;

">

🔒 Verification powered by

<b>

Withinhere

</b>

</small>



</div>



</div>



</div>



</div>




<div

class="modal fade"

id="verificationModal"

tabindex="-1">

<div

class="modal-dialog modal-sm"

style="

margin-top:

8%;

">

<div

class="modal-content"

style="

border:none;

border-radius:

20px;

overflow:

hidden;

">




<div

class="modal-header"

style="

background:

linear-gradient(

135deg,

#3c8dbc,

#00c0ef

);

color:

white;

">

<h4>

Verification Result

</h4>

</div>




<div

id="verificationBody"

class="modal-body"

style="

padding:

35px;

text-align:

center;

">

</div>




<div

class="modal-footer">

<button

class="btn btn-primary"

data-dismiss="modal">

OK

</button>

</div>



</div>

</div>

</div>




@endsection




@section('footer-scripts')


<script>



function getOperator(phoneNumber){

    let digit =

    phoneNumber.charAt(2);



    switch(digit){

        case "7":

        return "airtel";


        case "6":

        return "mtn";


        case "5":

        return "zamtel";


        default:

        return null;

    }

}




function verifyPhone(phone){


    let operator =

    getOperator(
        phone
    );



    if(

        phone.length !== 10

        ||

        !operator

    ){

        return;

    }




    $("#verificationBody")

    .html(`

        <div>

        ⏳

        </div>


        Checking...


        <br><br>


        <small>

        Powered by

        <b>

        Withinhere

        </b>

        </small>

    `);




    $("#verificationModal")

    .modal(
        "show"
    );





    $.ajax({


        url:

        "https://withinheremobileapi.com/api/v1/payment/resolve/mobile",


        type:

        "POST",


        contentType:

        "application/json",



        data:

        JSON.stringify({

            phone:

            phone,

            operator:

            operator

        }),




        success:function(response){



            $("#verificationBody")

            .html(`


                <div

                style="

                font-size:

                65px;

                ">

                ✅

                </div>




                <h3>

                Number Found

                </h3>




                <p>

                Registered Name:

                <br>


                <b>

                ${response.data.accountName}

                </b>

                </p>




                <p>

                Network:

                <b>

                ${response.data.operator.toUpperCase()}

                </b>

                </p>




                <div

                style="

                margin-top:

                20px;

                font-size:

                12px;

                color:

                #888;

                ">

                Powered by

                <b>

                Withinhere

                </b>

                </div>

            `);


        },




        error:function(){



            $("#verificationBody")

            .html(`


                <div

                style="

                font-size:

                65px;

                ">

                ❌

                </div>




                <h3>

                Verification Failed

                </h3>




                <p>

                Could not verify this number

                </p>




                <small>

                Powered by

                <b>

                Withinhere

                </b>

                </small>

            `);


        }



    });



}




let timeout;



$("#mobile")
.on(

    "input",

    function(){


        clearTimeout(
            timeout
        );



        let phone =

        $(this)

        .val()

        .replace(
            /\D/g,
            ""
        );



        timeout =

        setTimeout(

            ()=>{

                verifyPhone(
                    phone
                );

            },

            400

        );



    }

);



</script>


@endsection