# Motor Vehicle Loan Lifecycle System - Implementation Flow Chart

## Complete Loan Lifecycle Flow

```mermaid
flowchart TD
    A[Client Application] --> B[Loan Consultant<br/>Responsible Officer]
    B --> C[Originating Branch]
    C --> D[Branch Assessor]
    D --> E[District Manager]
    E --> F[Province Manager]
    F --> G[KYC & Compliance<br/>PEP/Sanctions Screening]
    G --> H[Vehicle Ownership<br/>Verification]
    H --> I[Vehicle Valuation]
    I --> J[Approval<br/>Workflow Engine]
    J --> K[Vehicle Intake<br/>& Custody Management]
    K --> L[Storage Location<br/>& Custodian Assignment]
    L --> M[Weekly Verification<br/>Roll Call]
    M --> N[Disbursement]
    N --> O[Repayment tracking]
    O --> P{Loan Performance}
    P -->|Performing| Q[Active Loan]
    P -->|At Risk| P1[Arrears]
    P1 --> P2[Default]
    P2 --> R[Recovery Process]
    R --> S[Notice Management<br/>Demand/Intention to Sell]
    R --> T[Traffic Offence<br/>Management]
    S --> U[Repossession]
    U --> V[Disposal Workflow]
    V --> W[Buyer Management]
    W --> X[Purchase Report<br/>Generation]
    X --> Y[Allocation of<br/>Sale Proceeds]
    Y --> Z[Final Closure]
    Q --> Z
    V --> Z
    
    classDef phase1 fill:#e1f5fe,stroke:#01579b
    classDef phase2 fill:#e8f5e9,stroke:#1b5e20
    classDef phase3 fill:#fff3e0,stroke:#e65100
    classDef phase4 fill:#fce4ec,stroke:#880e4f
    classDef phase5 fill:#f3e5f5,stroke:#4a148c
    classDef phase6 fill:#e0f2f1,stroke:#004d40
    classDef phase7 fill:#ffebee,stroke:#b71c1c
    classDef phase8 fill:#f1f8e9,stroke:#33691e
    classDef phase9 fill:#fff8e1,stroke:#ff6f00
    classDef phase10 fill:#e8EAF6,stroke:#1a237e
    classDef phase11 fill:#fbe9e7,stroke:#bf360c
    classDef phase12 fill:#f0f4c3,stroke:#827717
    
    class A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,P1,P2,R,S,T,U,V,W,X,Y,Z phase1
```

## Implementation Phases Flow

```mermaid
flowchart LR
    subgraph Sprint1[Sprint 1: Foundation]
        direction TB
        F1[Feature 1<br/>Motor Vehicle<br/>Loan Master Record]
        F2[Feature 2<br/>Loan Lifecycle<br/>Workflow Engine]
        F3[Feature 3<br/>Complete Audit Trail]
    end
    
    subgraph Sprint2[Sprint 2: Compliance &<br/>Product Rules]
        direction TB
        F4[Feature 4<br/>Client KYC &<br/>Compliance]
        F5[Feature 5<br/>PEP &<br/>Sanctions Screening]
        F6[Feature 6<br/>Configurable Loan<br/>Products]
        F7[Feature 7<br/>Approval Matrix<br/>Configuration]
    end
    
    subgraph Sprint3[Sprint 3: Vehicle<br/>Operations]
        direction TB
        F8[Feature 8<br/>Vehicle Ownership<br/>Verification]
        F9[Feature 9<br/>Vehicle Valuation<br/>Module]
        F10[Feature 10<br/>Vehicle Inspection<br/>Module]
        F11[Feature 11<br/>Insurance<br/>Management]
        F12[Feature 12<br/>Vehicle Intake<br/>Module]
        F13[Feature 13<br/>Vehicle Custody<br/>Register]
        F14[Feature 14<br/>Vehicle Movement<br/>Register]
        F15[Feature 15<br/>Weekly Vehicle<br/>Roll Call]
        F16[Feature 16<br/>Vehicle Photo<br/>Gallery]
        F17[Feature 17<br/>Document<br/>Repository]
    end
    
    subgraph Sprint4[Sprint 4: Loan<br/>Operations]
        direction TB
        F18[Feature 18<br/>Branch Referral<br/>Tracking]
        F19[Feature 19<br/>Loan Consultant<br/>Incentive Tracking]
        F20[Feature 20<br/>Re-loans &<br/>Add-On Loans]
        F21[Feature 21<br/>Arrears &<br/>Default Engine]
        F22[Feature 22<br/>Recovery<br/>Workflow]
        F23[Feature 23<br/>Notice<br/>Management]
        F24[Feature 24<br/>Traffic Offence<br/>Management]
    end
    
    subgraph Sprint5[Sprint 5: Disposal &<br/>Reporting]
        direction TB
        F25[Feature 25<br/>Vehicle Disposal<br/>Workflow]
        F26[Feature 26<br/>Buyer<br/>Management]
        F27[Feature 27<br/>Vehicle Purchase<br/>Report Generator]
        F28[Feature 28<br/>Sale Proceeds<br/>Allocation]
        F29[Feature 29<br/>SMS Notification<br/>Engine]
        F30[Feature 30<br/>Management<br/>Alerts]
        F31[Feature 31<br/>Motor Vehicle<br/>Portfolio Dashboard]
        F32[Feature 32<br/>Custody<br/>Dashboard]
        F33[Feature 33<br/>Recovery<br/>Dashboard]
        F34[Feature 34<br/>Profitability<br/>Dashboard]
        F35[Feature 35<br/>Motor Vehicle<br/>Settings Module]
        F36[Feature 36<br/>Motor Vehicle<br/>Audit & History Viewer]
    end
    
    S1[Sprint 1] --> S2[Sprint 2]
    S2 --> S3[Sprint 3]
    S3 --> S4[Sprint 4]
    S4 --> S5[Sprint 5]
```

## Phase 1: Foundation (Features 1-3)

```mermaid
flowchart TD
    subgraph MasterRecord[Feature 1: Motor Vehicle Loan<br/>Master Record]
        MR[Loan Record]
        MR --> C1[Client]
        MR --> V[Vehicle]
        MR --> LC[Loan Consultant<br/>Responsible Officer]
        MR --> OB[Originating Branch]
        MR --> BA[Branch Assessor]
        MR --> D[District]
        MR --> P[Province]
        MR --> LS[Loan Status]
        MR --> VS[Vehicle Status]
        MR --> CustS[Custody Status]
        MR --> SLOC[Storage Location]
        MR --> Cusc[Current Custodian]
    end
    
    subgraph Workflow[Feature 2: Loan Lifecycle<br/>Workflow Engine]
        WS[Workflow Stages]
        WS --> Draft[Draft/Application]
        WS --> Assess[Assessment]
        WS --> Appr[Approval]
        WS --> Intake[Vehicle Intake]
        WS --> Disbur[Disbursement]
        WS --> Act[Active Loan]
        WS --> Arr[Arrears]
        WS --> Def[Default]
        WS --> Rec[Recovery]
        WS --> DispPending[Disposal Pending]
        WS --> Sold[Sold]
        WS --> Closed[Closed]
        
        Tr[Transition Rules]
        Hist[Status History]
        Com[Comments]
        Off[Officer]
        Ts[Timestamp]
        ApprN[Approval Notes]
    end
    
    subgraph Audit[Feature 3: Complete Audit Trail]
        Act[Every Action]
        Act --> Cr[Created by]
        Act --> As[Assessed by]
        Act --> Ap[Approved by]
        Act --> Val[Valued by]
        Act --> Rcv[Vehicle received by]
        Act --> Mov[Vehicle moved by]
        Act --> Ver[Verified by]
        Act --> RecOf[Recovery officer]
        Act --> DispOf[Disposal officer]
        Act --> Clos[Closed by]
        
        Log[Log Info]
        Log --> PrevSt[Previous Status]
        Log --> NewSt[New Status]
        Log --> User[User]
        Log --> Branch[Branch]
        Log --> Reason[Action Reason]
        Log --> TS2[Timestamp]
    end
    
    MasterRecord --> Workflow
    Workflow --> Audit
```

## Phase 2: Customer Compliance & KYC

```mermaid
flowchart TD
    subgraph KYC[Feature 4: Client KYC &<br/>Compliance]
        KYC[KYC Fields]
        KYC --> NRC[NRC/Passport]
        KYC --> TPIN[TPIN]
        KYC --> Addr[Address]
        KYC --> Emp[Employer]
        KYC --> Biz[Business Details]
        KYC --> Inc[Income]
        KYC --> Phone[Phone numbers]
        KYC --> Email[Email]
        KYC --> NOK[Next of Kin]
        KYC --> Guar[Guarantor info]
        
        Upl[Document Uploads]
        Upl --> NRC_F[NRC Front]
        Upl --> NRC_B[NRC Back]
        Upl --> Selfie[Selfie]
        Upl --> UB[Utility Bill]
        Upl --> EL[Employment Letter]
        Upl --> Pay[Payslip]
    end
    
    subgraph PEP[Feature 5: PEP &<br/>Sanctions Screening]
        PEPResult[PEP Result]
        Sanct[Sanctions Result]
        ScrDate[Screening Date]
        ScrOff[Screening Officer]
        MatchL[Match Level]
        Comments[Comments]
        Evidence[Supporting Evidence]
        
        ScrFlow[PEP Workflow]
        ScrFlow --> Pend[Pending]
        ScrFlow --> Clr[Cleared]
        ScrFlow --> Flag[Flagged]
        ScrFlow --> Rvw[Requires Review]
    end
    
    KYC --> PEP
```

## Phase 3: Product Configuration

```mermaid
flowchart TD
    subgraph Prod[Feature 6: Configurable<br/>Motor Vehicle Loan Products]
        Bands[Loan Bands]
        MinLoan[Minimum Loan]
        MaxLoan[Maximum Loan]
        IntRate[Interest Rate]
        IntType[Interest Type]
        Tenure[Tenure]
        ServiceFee[Service Fee]
        ProcFee[Processing Fee]
        InsFee[Insurance Fee]
        ValFee[Valuation Fee]
        InsFee2[Inspection Fee]
        PenRate[Penalty Rate]
        RecCharge[Recovery Charges]
        
        Req[Requirements]
        Req --> EffDate[Effective Date]
        Req --> Ver[Version History]
        Req --> ActInact[Active/Inactive]
        Req --> Approval[Approval before activation]
    end
    
    subgraph ApprMat[Feature 7: Approval Matrix<br/>Configuration]
        Limits[Branch Limits]
        DistLimits[District Limits]
        ProvLimits[Province Limits]
        HOLimits[Head Office Limits]
        
        Levels[Approval Levels]
        Levels --> LO[Loan Officer]
        Levels --> BM[Branch Manager]
        Levels --> DM[District Manager]
        Levels --> PM[Province Manager]
        Levels --> RM[Risk Manager]
        Levels --> EC[Executive Committee]
    end
    
    Prod --> ApprMat
```

## Phase 4: Vehicle Operations

```mermaid
flowchart TD
    subgraph Own[Feature 8: Vehicle Ownership<br/>Verification]
        OwnType[Ownership Types]
        OwnType --> Ind[Individual]
        OwnType --> LoS[Letter of Sale]
        OwnType --> Corp[Company/Corporate]
        
        IndReq[Individual Requirements]
        IndReq --> RegOwner[Registered Owner]
        IndReq --> S[ Seller]
        IndReq --> Docs[Ownership Documents]
        
        LSRq[Letter of Sale Requirements]
        LSRq --> LUL[Letter upload]
        LSRq --> SvV[Seller verification]
        LSRq --> Wit[Witnesses]
        
        CorpReq[Corporate Requirements]
        CorpReq --> CR[Company Registration]
        CorpReq --> Dir[Directors]
        CorpReq --> Res[Resolution]
        CorpReq --> AR[Authorized Representative]
    end
    
    subgraph Val[Feature 9: Vehicle Valuation<br/>Module]
        ValComp[Valuation Company]
        Valuat[Valuator]
        MktVal[Market Value]
        FsVal[Forced Sale Value]
        ValCost[Valuation Cost]
        ExpDate[Expiry Date]
        
        ValUpl[Uploads]
        ValUpl --> ValRprt[Valuation Report]
        ValUpl --> Photos[Photos]
        ValUpl --> Docs2[Supporting Docs]
        
        ValHist[Valuation history]
        ExpAlert[Expiry alerts]
    end
    
    subgraph Insp[Feature 10: Vehicle Inspection<br/>Module]
        InstDate[Inspection Date]
        Inspector[Inspector]
        Mileage[Mileage]
        Mech[Mechanical Condition]
        Int[Interior]
        Ext[Exterior]
        Tires[Tyres]
        Batt[Battery]
        Acc[Accessories]
        
        InspUpl[Upload]
        InspUpl --> InspRprt[Inspection Report]
        InspUpl --> InspPhotos[Inspection Photos]
        
        Check[Inspection checklist]
        CondScore[Condition score]
    end
    
    subgraph Ins[Feature 11: Insurance<br/>Management]
        InsComp[Insurance Company]
        PolNum[Policy Number]
        StDate[Start Date]
        EndDate[End Date]
        Prem[Premium]
        Cover[Cover Type]
        
        InsAlert[Expiring/Expired insurance]
    end
    
    subgraph Intake[Feature 12: Vehicle Intake<br/>Module]
        IntDate[Intake Date]
        RcvOf[Receiving Officer]
        Cond[Condition]
        Keys[Keys Received]
        DocsR[Documents Received]
        AccR[Accessories Received]
        Fuel[Fuel Level]
        
        IntUpl[Upload]
        IntUpl --> IntPhotos[Intake Photos]
        IntUpl --> SIF[Signed Intake Form]
        
        Checklist[Intake checklist]
        IntRpt[Intake report PDF]
    end
    
    subgraph Cust[Feature 13: Vehicle Custody<br/>Register]
        SLoc[Storage Location]
        GPS[GPS/Location Description]
        HO[House/Garage Owner]
        CN[Custodian Name]
        NRC2[NRC]
        PH[Phone]
        AltContact[Alternative Contact]
        SDS[Storage Start Date]
        Notes[Notes]
        
        CustReg[Custody Register]
        CCV[Current Custody Card]
        CustHist[Custody history]
    end
    
    subgraph Mov[Feature 14: Vehicle Movement<br/>Register]
        PrevLoc[Previous Location]
        NewLoc[New Location]
        MovDate[Movement Date]
        AuthBy[Authorized By]
        MovBy[Moved By]
        Reason[Reason]
        Cond2[Condition]
        Photos2[Photos]
        
        MovHist[Movement history]
        TransferApp[Transfer approval workflow]
    end
    
    subgraph RollCall[Feature 15: Weekly Vehicle<br/>Roll Call]
        VerifDate[Verification Date]
        Officer[Officer]
        LocConf[Location Confirmed]
        VehPres[Vehicle Present]
        Cond3[Condition]
        Mileage2[Current Mileage]
        Photos3[Photos]
        
        Status[Verified/ Missing/ Damaged/ Relocated]
    end
    
    subgraph Gallery[Feature 16: Vehicle Photo<br/>Gallery]
        Cat[Categories: Intake, Verification, Inspection, Valuation, Recovery, Disposal]
        DateP[Date]
        Officer2[Officer]
        Caption[Caption]
        
        Gallery[Gallery]
        Timeline[Timeline]
    end
    
    subgraph DocRep[Feature 17: Document<br/>Repository]
        Docs[Store: NRC, Valuation Reports, Insurance, Letters of Sale, Registration, Road Tax, Fitness, Recovery Notices, Sale Documents]
        
        DocMgr[Document manager]
        ExpTrack[Expiry tracker]
        DV[Download/view]
    end
    
    Own --> Val --> Insp --> Ins --> Intake --> Cust --> Mov --> RollCall > Gallery
    Gallery --> DocRep
```

## Phase 5: Loan Operations

```mermaid
flowchart TD
    subgraph Ref[Feature 18: Branch Referral<br/>Tracking]
        RefBranch[Referring Branch]
        RecBranch[Receiving Branch]
        RefOfficer[Referral Officer]
        RefDate[Referral Date]
        Notes2[Notes]
        
        RefHist[Referral history]
        RefReports[Referral reports]
    end
    
    subgraph Incent[Feature 19: Loan Consultant<br/>Incentive Tracking]
        Rules[Rules]
        IncentCalc[Incentive calculated]
        Pend2[Pending]
        Earned[Earned]
        Payable[Payable only after full settlement]
        Paid[Paid]
        
        IncentLedger[Incentive ledger]
        SettleVal[Settlement validation]
        PayrollExp[Payroll export]
    end
    
    subgraph TopUp[Feature 20: Re-loans &<br/>Add-On Loans]
        TopUpType[Top Up / Re-loan]
        Parent[Linked parent loan]
        Val2[Valuation]
        Ins2[Insurance]
        Perf[Loan performance]
        OutBal[Outstanding balance]
        
        ChainHist[Loan chain history]
        Elig[Eligibility checker]
    end
    
    Ref --> Incent --> TopUp
```

## Phase 6: Default & Recovery

```mermaid
flowchart TD
    subgraph Arrears[Feature 21: Arrears &<br/>Default Engine]
        States[Automatic States]
        States --> Due[Due]
        States --> Over[Overdue]
        States --> Arrears[Arrears]
        States --> DefDefault[Default]
        
        Rules[Configurable Rules]
        Grace[Grace Period]
        D2Def[Days to Default]
        Penalty[Penalty]
        
        Calc[Default calculator]
        Dash[Arrears dashboard]
    end
    
    subgraph Rec[Feature 22: Recovery<br/>Workflow]
        Stages[Recovery Stages]
        Stages --> Rem[Reminder]
        Stages --> DN[Demand Notice]
        Stages --> NOI[Notice of Intention to Sell]
        Stages --> Rep[Repossession]
        Stages --> RecProc[Recovery in Progress]
        Stages --> RecComp[Recovery Complete]
        
        RecCase[Recovery case module]
        OfficerAss[Officer assignment]
        RecTime[Timeline]
    end
    
    subgraph Notice[Feature 23: Notice<br/>Management]
        AutoGen[Auto Generate]
        AutoGen --> RemL[Reminder Letters]
        AutoGen --> DemL[Demand Letters]
        AutoGen --> INT[Intent to Sell]
        AutoGen --> Final[Final Notices]
        
        PDF[PDF generation]
        SMStrig[SMS trigger]
        ET[Email trigger]
    end
    
    subgraph Traffic[Feature 24: Traffic Offence<br/>Management]
        Offence[Offence]
        Date2[Date]
        Amount[Amount]
        PaidBy[Paid By]
        Status2[Status]
        Receipt[Receipt]
        
        Ledger[Traffic offence ledger]
        OutStanding[Outstanding offences report]
    end
    
    Arrears --> Rec --> Notice
    Rec --> Traffic
```

## MVL Entry via Client Loan Creation (Integration)

```mermaid
flowchart LR
    subgraph CreateLoan[Create Client Loan (create_client_loan.blade.php)]
        CL[Client Loan Form]
        CL --> CheckProd{Is Motor Vehicle<br/>Loan Product?}
        CheckProd -->|Yes| Collateral[@if: Hide Collateral Field]
        CheckProd -->|Yes| MVLField[Add mvl_next hidden field = 1]
        CheckProd -->|Yes| SubmitBtn[Submit Button: "Save and proceed to KYC/PEP verification"]
        CheckProd -->|No| DefaultBtn[Submit Button: "Submit"]
    end

    subgraph LoanController[LoanController::storeClientLoan]
        Create[Create Loan Record]
        CheckMVL{mvl_next == 1?}
        CheckMVL -->|Yes| CreateMVL[Create MotorVehicleLoan Record]
        CreateMVL --> SetFields[loan_id, client_id, status=draft]
        SetFields --> Redirect[Redirect to compliance-screening]
        Redirect --> UseMVL[Use MotorVehicleLoan->id]
        CheckMVL -->|No| CheckCollateral{has_collateral?}
        CheckCollateral -->|Yes| RedirectCollateral[Redirect to collateral/create]
        CheckCollateral -->|No| RedirectLoan[Redirect to loan/{id}/show]
    end

    subgraph DBMigr[Migrations]
        VehicleID[make vehicle_id nullable]
        VehicleID --> MVLTable[motor_vehicle_loans table]
    end

    CreateLoan --> LoanController
    LoanController --> DBMigr

    style CreateLoan fill:#e1f5fe,stroke:#01579b
    style LoanController fill:#e8f5e9,stroke:#1b5e20
    style DBMigr fill:#fff3e0,stroke:#e65100
```

### Compliance Screening Route

```mermaid
flowchart LR
    A[LoanController] --> B[Rouverse to]
    B --> C[motor-vehicle-loans/{id}<br/>/compliance-screening]
    C --> D[MotorVehicleLoanLifecycleController<br/>@complianceScreening]
    D --> E[Load MotorVehicleLoan by ID<br/>+ load Client relationship]
    E --> F[View: motor_vehicle.loan_lifecycle.<br/>compliance_screening]

    style A fill:#e1f5fe,stroke:#01579b
    style D fill:#e8f5e9,stroke:#1b5e20
    style F fill:#fff3e0,stroke:#e65100
```

### Files Modified

| File | Change |
|------|--------|
| `resources/views/loan/create_client_loan.blade.php` | Added `@if($loan_product->id != 0)` around collateral field; added `mvl_next` hidden field; conditional submit button text |
| `app/Http/Controllers/LoanController.php` (line 1638-1646) | Creates `MotorVehicleLoan` record with `loan_id`, `client_id`, `status='draft'`; redirects using `$mvl->id` |
| `database/migrations/2026_09_02_115700_make_vehicle_id_nullable_in_motor_vehicle_loans_table.php` | Makes `vehicle_id` nullable in `motor_vehicle_loans` table |

## Phase 5: Disposal & Sale Management

```mermaid
flowchart TD
    subgraph Dispose[Feature 25: Vehicle Disposal<br/>Workflow]
        Stages[Stages]
        Stages --> DispApp[Disposal Approved]
        Stages --> ValDisp[Valuation]
        Stages --> Sale[Sale]
        Stages --> Pay[Payment]
        Stages --> Rel[Release]
        Stages --> Closed2[Closed]
        
        AppRec[Approval records]
    end
    
    subgraph Buyer[Feature 26: Buyer<br/>Management]
        BuyerDet[Full Name]
        BuyerNRC[NRC]
        BuyerAddr[Address]
        BuyerPhone[Phone]
        BuyerEmail[Email]
        BuyerComp[Company Details]
        
        BuyerProf[Buyer profile]
        BuyerHist[Buyer history]
    end
    
    subgraph PurchRep[Feature 27: Vehicle Purchase<br/>Report Generator]
        AutoPDF[Auto Generate PDF]
        
        Contents[Include: Buyer details, Vehicle details, Purchase price, As-Is declaration, Signatures, Witnesses, Sale date]
        
        PrintRep[Printable report]
        DigCopy[Digital copy storage]
    end
    
    subgraph Alloc[Feature 28: Sale Proceeds<br/>Allocation]
        SaleAmt[Sale Amount]
        Princ[Outstanding Principal]
        Int[Interest]
        Pen[Penalties]
        RecCost[Recovery Costs]
        StorCost[Storage Costs]
        LegCost[Legal Costs]
        Surpl[Surplus]
        
        AllocJ[Allocation journal]
        DispRpt[Disposal financial report]
    end
    
    Dispose --> Buyer --> PurchRep --> Alloc
```

## Phase 8: Notifications & Automation

```mermaid
flowchart TD
    subgraph SMSN[Feature 29: SMS Notification<br/>Engine]
        ClientSMS[Client SMS]
        ClientSMS --> LoanAppr[Loan approved]
        ClientSMS --> Disburse[Disbursed]
        ClientSMS --> PayDue[Payment due]
        ClientSMS --> Overdue[Overdue]
        ClientSMS --> Def[Default]
        ClientSMS --> RecNot[Recovery notice]
        ClientSMS --> VehSold[Vehicle sold]
        ClientSMS --> LoanClosed[Loan closed]
        
        SMSTpl[SMS templates]
        SMSLog[SMS logs]
    end
    
    subgraph Alerts[Feature 30: Management<br/>Alerts]
        Notify[Notify Managers When]
        Notify --> VehMov[Vehicle moved]
        Notify --> RollMiss[Roll-call missed]
        Notify --> InsExp[Insurance expired]
        Notify --> ValExp[Valuation expired]
        Notify --> DefTrig[Default triggered]
        Notify --> RecOver[Recovery overdue]
        Notify --> DispComp[Disposal completed]
        
        NotifyCenter[Notification center]
        EmailSMS[Email/SMS alerts]
    end
    
    SMSN --> Alerts
```

## Phase 9: Reporting & Dashboards

```mermaid
flowchart TD
    subgraph Portfol[Feature 31: Motor Vehicle<br/>Portfolio Dashboard]
        KPIs[KPIs]
        KPIs --> PortVal[Portfolio Value]
        KPIs --> OutBal2[Outstanding Balance]
        KPIs --> ActLoans[Active Loans]
        KPIs --> AArrears[Arrears]
        KPIs --> Defaults[Defaults]
        KPIs --> Recov[Recoveries]
        KPIs --> Dispos[Disposals]
        KPIs --> Profit[Profitability]
        
        Filters[Filters]
        Filters --> Prov2[Province]
        Filters --> Dist2[District]
        Filters --> Branch2[Branch]
        Filters --> Officer3[Officer]
        Filters --> DateRange[Date Range]
    end
    
    subgraph CustDash[Feature 32: Custody<br/>Dashboard]
        InCust[Vehicles in custody]
        ByProv[By Province]
        ByDist[By District]
        ByBranch[By Branch]
        Missing[Missing vehicles]
        VerComp[Verification compliance]
        Storage[Storage occupancy]
    end
    
    subgraph RecDash[Feature 33: Recovery<br/>Dashboard]
        RecPipe[Recovery pipeline]
        Notices[Notices issued]
        Reposs[Repossessions]
        AWAIT[ehicles awaiting sale]
        SaleProv[Sale proceeds]
        RecSucc[Recovery success rate]
    end
    
    subgraph ProfitDash[Feature 34: Profitability<br/>Dashboard]
        IntEarn[Interest earned]
        FeesEarn[Fees earned]
        RecInc[Recovery income]
        DispInc[Disposal income]
        RecExp[Recovery expenses]
        StorExp[Storage expenses]
        NetProfit[Net profitability]
    end
    
    Portfol --> CustDash --> RecDash --> ProfitDash
```

## Phase 10: Administration & Configuration

```mermaid
flowchart TD
    subgraph Settings[Feature 35: Motor Vehicle<br/>Settings Module]
        ConfigItems[Configurable Items]
        ConfigItems --> IntRate2[Interest Rates]
        ConfigItems --> LoanBands2[Loan Bands]
        ConfigItems --> Tenure2[Tenure]
        ConfigItems --> Fees[Fees]
        ConfigItems --> Penalties[Penalties]
        ConfigItems --> RecTimelines[Recovery timelines]
        ConfigItems --> NoticePer[Notice periods]
        ConfigItems --> ApprLimits[Approval limits]
        ConfigItems --> IncentPct[Incentive percentages]
        ConfigItems --> StorageVer[Storage verification frequency]
        
        PerfDate[Effective date management]
        VerHist2[Version history]
    end
    
    subgraph AuditView[Feature 36: Motor Vehicle<br/>Audit & History Viewer]
        Timeline[Single Timeline Showing]
        Timeline --> Client2[Client]
        Timeline --> Staff[Staff Member]
        Timeline --> Branch3[Branch]
        Timeline --> Assessor[Branch-Assessor]
        Timeline --> District2[District]
        Timeline --> Province3[Province]
        Timeline --> KYC2[KYC]
        Timeline --> Ownership[Ownership Verification]
        Timeline --> Val2[Valuation]
        Timeline --> Appr2[Approval]
        Timeline --> Intake2[Intake]
        Timeline --> Storage[Storage Location]
        Timeline --> Cust2[Custodian]
        Timeline --> WeekVer[Weekly Verification]
        Timeline --> Disburse2[Disbursement]
        Timeline --> Repay[Repayment]
        Timeline --> Default2[Default]
        Timeline --> Rec2[Recovery]
        Timeline --> Dispo[Disposal]
        Timeline --> Buyer2[Buyer]
        Timeline --> Closure[Closure]
        
        ExpandHist[Expandable event history]
        DocsPhotos[Linked documents/photos]
        OfficerAT[Officer audit trail]
    end
    
    Settings --> AuditView
```

## Complete Implementation Sequence

```mermaid
gantt
    title Motor Vehicle Loan Lifecycle System - Implementation Timeline
    dateFormat  YYYY-MM-DD
    section Sprint 1
    Foundation :done, des1, 2024-01-01, 30d
    section Sprint 2
    Compliance & Product Rules :active, des2, 2024-02-01, 30d
    section Sprint 3
    Vehicle Operations : des3, 2024-03-01, 60d
    section Sprint 4
    Loan Operations : des4, 2024-05-01, 60d
    section Sprint 5
    Disposal & Reporting : des5, 2024-07-01, 60d
```