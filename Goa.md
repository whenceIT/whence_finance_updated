

The system should primarily answer:

What assets does each branch have, how many do they have, how many are working/usable, how many are damaged, and what needs attention?

1. Branch Asset Inventory

For each branch, we should have an inventory showing the different categories of assets and their quantities.

For example:

CHIPATA BRANCH – ASSET INVENTORY

| Asset              | Total Quantity | Good/Working | Damaged | Missing | Under Repair |
| ------------------ | -------------: | -----------: | ------: | ------: | -----------: |
| Microwave          |              1 |            1 |       0 |       0 |            0 |
| Water Dispenser    |              2 |            1 |       1 |       0 |            0 |
| LC Office Desks    |              7 |            6 |       1 |       0 |            0 |
| LC Office Tables   |              7 |            7 |       0 |       0 |            0 |
| Visitors Chairs    |             12 |           10 |       2 |       0 |            0 |
| Managers Chairs    |              2 |            2 |       0 |       0 |            0 |
| Managers Tables    |              2 |            2 |       0 |       0 |            0 |
| Executive Chairs   |              3 |            2 |       1 |       0 |            0 |
| Tablets            |              7 |            6 |       1 |       0 |            0 |
| Phones             |              5 |            5 |       0 |       0 |            0 |
| Fire Extinguishers |              4 |            4 |       0 |       0 |            0 |
| Office Trays       |             15 |           14 |       1 |       0 |            0 |
| Laptops            |              4 |            4 |       0 |       0 |            0 |
| Monitors           |              5 |            5 |       0 |       0 |            0 |
| Desktop Computers  |              2 |            2 |       0 |       0 |            0 |
| Wall Frames        |              6 |            6 |       0 |       0 |            0 |
| Coffee Tables      |              2 |            2 |       0 |       0 |            0 |
| Office Cabinets    |              5 |            5 |       0 |       0 |            0 |
| Electric Kettles   |              3 |            2 |       1 |       0 |            0 |

This would give Administration an immediate picture of the condition of the branch's assets.

2. Asset Categories

The system should have a standard list of common company assets, including:

* Microwave
* Water Dispenser
* LC Office Desks
* LC Office Tables
* Visitors Chairs
* Manager's Chair
* Manager's Table
* Executive Chairs
* Tablets
* Phones
* Fire Extinguishers
* Office Trays
* Laptops
* Monitors
* Desktop Computers
* Wall Frames
* Coffee Tables
* Office Cabinets
* Electric Kettles
* And other assets that may be added later

The list should be editable so Administration can add new asset categories when necessary.

 3. We Should Track Quantities, Not Every Individual Item

For ordinary office assets, the main record should be quantity-based.

For example:

Tablets – Chipata Branch

Total: 7
Working: 6
Damaged: 1
Missing: 0

There is no need for Administration to create seven separate asset records simply because there are seven tablets.

However, for assets such as tablets, laptops, phones and desktop computers, where serial numbers or individual assignment is important, the system should optionally allow individual details to be captured.

For example:

Tablets – 7

6 Working
1 Damaged

Then, if necessary, we can click "View Details" and see:

Tablet 1 – Working – Assigned to LC
Tablet 2 – Working – Assigned to LC
Tablet 3 – Working – Assigned to LC
etc.

This detailed level should be optional rather than mandatory for every asset category.

4. Damage Reporting

The damage reporting function should work against the asset category and branch.

For example, a manager could report:

Branch: Chipata
Asset: Water Dispenser
Quantity affected: 1
Date: 21 September 2026
Description: Not cooling water
Photo: Attached
Status: Reported

The system would then automatically update:

Water Dispensers:

Total: 2 | Working: 1 | Damaged: 1

5. Repair Tracking

When the damaged item is repaired, Administration should be able to update the record.

For example:

Water Dispenser

Reported damaged → Sent for repair → Repaired → Returned to branch

The system should record:

* Repair date
* Repair cost
* Repair provider
* Description of repair
* Invoice/receipt
* Date returned
* Current condition

The quantity would then automatically move from Damaged to Working.

6. Asset Damage History

The system should keep a history of damage reports by branch and asset category.

For example:

Chipata Branch – Water Dispenser

2026-03-10 – Damaged – K450 repair
2026-09-21 – Damaged – Awaiting assessment

This allows Administration to see recurring problems without having to maintain a separate spreadsheet.

7. Branch Asset Summary

Each branch should have a simple summary at the top:

CHIPATA BRANCH

Total Asset Categories: 20
Total Items: 95
Working/Usable: 87
Damaged: 6
Under Repair: 1
Missing: 1

Asset Condition: 91.6% usable

8. Company-Wide Asset Dashboard

At Administration level, we should be able to see all branches together.

For example:

TOTAL ASSETS ACROSS ALL BRANCHES

Total Items: 3,850
Working/Usable: 3,540
Damaged: 180
Under Repair: 75
Missing: 55

Then we should be able to filter by:

* Branch
* Province
* Asset category
* Condition
* Status

For example:

Tablets across Whence

Total: 280
Working: 256
Damaged: 15
Missing: 6
Under Repair: 3

 9. Damage Analytics

The dashboard should automatically analyse the reports and show:

* Number of damaged items this month
* Number of damaged items this year
* Most frequently damaged asset categories
* Branches with the most damage reports
* Total repair expenditure
* Repair expenditure by branch
* Repair expenditure by asset category
* Outstanding damage reports
* Assets currently under repair
* Missing assets

10. "Attention Required"

There should also be an alert section.

For example:

ATTENTION REQUIRED

🔴 Chipata – 6 damaged assets awaiting action

🔴 Mongu – 3 tablets reported missing

🟠 Ndola – 4 laptops under repair

🟠 Lusaka – K8,500 in outstanding asset repair costs

🟡 5 branches have not submitted their latest asset inventory

 11. Periodic Asset Verification

I would also like Administration to be able to request a branch to update/verify its asset inventory.

For example:

September 2026 Asset Verification

Branch Manager confirms:

☑️ Microwave – 1
☑️ Water Dispenser – 2
☑️ LC Desks – 7
☑️ Tablets – 7
☑️ Laptops – 4
☑️ Chairs – 12

The manager can then submit the inventory electronically.

This will give Administration a record of who verified the branch assets and when.

The principle I want the system to follow

For common office assets, we should use:

ASSET CATEGORY + QUANTITY + CONDITION

rather than creating a separate record for every physical item.

For example:

Tablets – 7

rather than:

Tablet 1
Tablet 2
Tablet 3
Tablet 4
Tablet 5
Tablet 6
Tablet 7

For assets that require individual accountability, such as laptops, tablets and phones, individual identification can be available as an optional sub-record using serial number/IMEI/asset number.

The main dashboard should therefore remain simple and quantity-based, while still allowing more detailed tracking where it is necessary.



















@app/Models/Office.php structure:
id 
name
parent_id
external_id
opening_date
branch_capacity (Approved Capacity)
address
phone
email
notes
manager_id
active
default_office
created_at
updated_at
deleted_at
province_id
district_id
district_regional_id
withinhere_branch_id
withinhere_wallet_id
pscan
cscan
workstations
