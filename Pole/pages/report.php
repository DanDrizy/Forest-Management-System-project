<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('pole plant');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pole Plant Report</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .menu-item.active-report {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }

        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-btn {
            padding: 8px 12px;
            background: #00dc82;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .export-options {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
            border-radius: 5px;
            overflow: hidden;
            z-index: 1;
        }

        .export-options div {
            padding: 10px 20px;
            cursor: pointer;
            color: #333;
            white-space: nowrap;
        }

        .export-options div:hover {
            background-color: #f0f0f0;
        }

        .export-dropdown:hover .export-options {
            display: block;
        }

        .download-buttons {
            display: flex;
            justify-content: flex-end;
            margin: 10px 0;
        }
        .container-export-icon
        {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .container-export-icon svg
        {
            width: 20px;
            height: 20px;
            fill: #00dc82;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php 
    include '../menu/menu.php';
    include '../../database/connection.php';

    $select_pole_records = $pdo->query("SELECT * FROM pole");
    $select_pole_records->execute();
    $pole_records = $select_pole_records->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    ?>

    <div class="main-content">
        <?php include '../header/header.php'; ?>

        <div class="dashboard-grid-saw">
            <div class="container">
                <h1>Pole Plant Report</h1>

                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" placeholder="Search stock entries..." id="searchInput">
                    </div>
                </div>

                <!-- Export Buttons -->
                <div class="download-buttons">
                    <div class="export-dropdown">
                        <button class="export-btn">Export ▾</button>
                        <div class="export-options">
                            <div class="container-export-icon" onclick="downloadPDF()">
                                
                            <svg viewBox="-4 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M25.6686 26.0962C25.1812 26.2401 24.4656 26.2563 23.6984 26.145C22.875 26.0256 22.0351 25.7739 21.2096 25.403C22.6817 25.1888 23.8237 25.2548 24.8005 25.6009C25.0319 25.6829 25.412 25.9021 25.6686 26.0962ZM17.4552 24.7459C17.3953 24.7622 17.3363 24.7776 17.2776 24.7939C16.8815 24.9017 16.4961 25.0069 16.1247 25.1005L15.6239 25.2275C14.6165 25.4824 13.5865 25.7428 12.5692 26.0529C12.9558 25.1206 13.315 24.178 13.6667 23.2564C13.9271 22.5742 14.193 21.8773 14.468 21.1894C14.6075 21.4198 14.7531 21.6503 14.9046 21.8814C15.5948 22.9326 16.4624 23.9045 17.4552 24.7459ZM14.8927 14.2326C14.958 15.383 14.7098 16.4897 14.3457 17.5514C13.8972 16.2386 13.6882 14.7889 14.2489 13.6185C14.3927 13.3185 14.5105 13.1581 14.5869 13.0744C14.7049 13.2566 14.8601 13.6642 14.8927 14.2326ZM9.63347 28.8054C9.38148 29.2562 9.12426 29.6782 8.86063 30.0767C8.22442 31.0355 7.18393 32.0621 6.64941 32.0621C6.59681 32.0621 6.53316 32.0536 6.44015 31.9554C6.38028 31.8926 6.37069 31.8476 6.37359 31.7862C6.39161 31.4337 6.85867 30.8059 7.53527 30.2238C8.14939 29.6957 8.84352 29.2262 9.63347 28.8054ZM27.3706 26.1461C27.2889 24.9719 25.3123 24.2186 25.2928 24.2116C24.5287 23.9407 23.6986 23.8091 22.7552 23.8091C21.7453 23.8091 20.6565 23.9552 19.2582 24.2819C18.014 23.3999 16.9392 22.2957 16.1362 21.0733C15.7816 20.5332 15.4628 19.9941 15.1849 19.4675C15.8633 17.8454 16.4742 16.1013 16.3632 14.1479C16.2737 12.5816 15.5674 11.5295 14.6069 11.5295C13.948 11.5295 13.3807 12.0175 12.9194 12.9813C12.0965 14.6987 12.3128 16.8962 13.562 19.5184C13.1121 20.5751 12.6941 21.6706 12.2895 22.7311C11.7861 24.0498 11.2674 25.4103 10.6828 26.7045C9.04334 27.3532 7.69648 28.1399 6.57402 29.1057C5.8387 29.7373 4.95223 30.7028 4.90163 31.7107C4.87693 32.1854 5.03969 32.6207 5.37044 32.9695C5.72183 33.3398 6.16329 33.5348 6.6487 33.5354C8.25189 33.5354 9.79489 31.3327 10.0876 30.8909C10.6767 30.0029 11.2281 29.0124 11.7684 27.8699C13.1292 27.3781 14.5794 27.011 15.985 26.6562L16.4884 26.5283C16.8668 26.4321 17.2601 26.3257 17.6635 26.2153C18.0904 26.0999 18.5296 25.9802 18.976 25.8665C20.4193 26.7844 21.9714 27.3831 23.4851 27.6028C24.7601 27.7883 25.8924 27.6807 26.6589 27.2811C27.3486 26.9219 27.3866 26.3676 27.3706 26.1461ZM30.4755 36.2428C30.4755 38.3932 28.5802 38.5258 28.1978 38.5301H3.74486C1.60224 38.5301 1.47322 36.6218 1.46913 36.2428L1.46884 3.75642C1.46884 1.6039 3.36763 1.4734 3.74457 1.46908H20.263L20.2718 1.4778V7.92396C20.2718 9.21763 21.0539 11.6669 24.0158 11.6669H30.4203L30.4753 11.7218L30.4755 36.2428ZM28.9572 10.1976H24.0169C21.8749 10.1976 21.7453 8.29969 21.7424 7.92417V2.95307L28.9572 10.1976ZM31.9447 36.2428V11.1157L21.7424 0.871022V0.823357H21.6936L20.8742 0H3.74491C2.44954 0 0 0.785336 0 3.75711V36.2435C0 37.5427 0.782956 40 3.74491 40H28.2001C29.4952 39.9997 31.9447 39.2143 31.9447 36.2428Z" fill="#EB5757"></path> </g></svg>
                            PDF</div>
                            <div onclick="downloadDOCX()" class="container-export-icon">

                            <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:#12c8fd;}</style> </defs> <title></title> <g id="xxx-word"> <path class="cls-1" d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z"></path> <path class="cls-1" d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z"></path> <path class="cls-1" d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z"></path> <path class="cls-1" d="M179.67,182.68,165.41,236H155.33l-10.62-39.22L135.06,236h-9.88l-14.57-53.32h10.2l10.31,38.87,9.61-38.87h9.73l10.63,38.87,10.12-38.87Z"></path> <path class="cls-1" d="M199.08,236.82q-8.75,0-13.36-6.29a23.75,23.75,0,0,1-4.61-14.41,21.32,21.32,0,0,1,5.1-14.57,17,17,0,0,1,13.46-5.82,16.75,16.75,0,0,1,13,5.66q5.1,5.66,5.1,14.73,0,9.34-5.29,15A17.54,17.54,0,0,1,199.08,236.82Zm.31-7.34q9,0,9-13.4,0-6.05-2.15-9.55a7.21,7.21,0,0,0-6.6-3.5,7.47,7.47,0,0,0-6.84,3.61q-2.23,3.61-2.23,9.59,0,6.45,2.36,9.84A7.46,7.46,0,0,0,199.39,229.48Z"></path> <path class="cls-1" d="M234.86,236H226V196.55h8V206q1.72-5.51,4.73-8a9.52,9.52,0,0,1,6.17-2.54l1.17,0V205q-6.8,0-9,4.34a18.47,18.47,0,0,0-2.21,8.4Z"></path> <path class="cls-1" d="M284.9,236h-8.32v-8q-3.44,8.79-11.64,8.79a12.43,12.43,0,0,1-11.13-6.05q-3.87-6.05-3.87-15a26.2,26.2,0,0,1,4-14.57,12.86,12.86,0,0,1,11.45-6.21q7.62,0,10.59,7V182.68h8.91ZM276,212.72q0-4.8-2.29-7.48a7.42,7.42,0,0,0-5.92-2.68,7,7,0,0,0-6.17,3.44q-2.23,3.44-2.23,10.27,0,13.2,8.28,13.2a7.58,7.58,0,0,0,5.8-2.83A10.49,10.49,0,0,0,276,219.4Z"></path> </g> </g></svg>
                                
                            Word</div>
                            <div onclick="downloadExcel()" class="container-export-icon">
                            <svg height="200px" width="200px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 26 26" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path style="fill:#030104;" d="M25.162,3H16v2.984h3.031v2.031H16V10h3v2h-3v2h3v2h-3v2h3v2h-3v3h9.162 C25.623,23,26,22.609,26,22.13V3.87C26,3.391,25.623,3,25.162,3z M24,20h-4v-2h4V20z M24,16h-4v-2h4V16z M24,12h-4v-2h4V12z M24,8 h-4V6h4V8z"></path> <path style="fill:#030104;" d="M0,2.889v20.223L15,26V0L0,2.889z M9.488,18.08l-1.745-3.299c-0.066-0.123-0.134-0.349-0.205-0.678 H7.511C7.478,14.258,7.4,14.494,7.277,14.81l-1.751,3.27H2.807l3.228-5.064L3.082,7.951h2.776l1.448,3.037 c0.113,0.24,0.214,0.525,0.304,0.854h0.028c0.057-0.198,0.163-0.492,0.318-0.883l1.61-3.009h2.542l-3.037,5.022l3.122,5.107 L9.488,18.08L9.488,18.08z"></path> </g> </g></svg>  
                            Excel</div>
                        </div>
                    </div>
                </div>

                <div id="report-content">
                    <table id="stockTable">
                        <thead>
                            <tr>
                                <th class="row-id">ID</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Measures</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pole_records as $record): ?>
                            <tr>
                                <td class="row-id"><?php echo $i++; ?></td>
                                <td><?php echo $record['tree_name']; ?></td>
                                <td><?php echo $record['record_date']; ?></td>
                                <td><?php echo $record['po_amount']; ?></td>
                                <td><?php echo $record['height']; ?></td>
                                <td><?php echo $record['location']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/check.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- Export Functions -->
    <script>
        function downloadPDF() {
            const element = document.getElementById('report-content');
            html2pdf().from(element).set({
                margin: 0.5,
                filename: 'pole_plant_report.pdf',
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
            }).save();
        }

        function downloadDOCX() {
            const content = document.getElementById('report-content').innerHTML;
            const converted = htmlDocx.asBlob('<html><body>' + content + '</body></html>');
            saveAs(converted, 'pole_plant_report.docx');
        }

        function downloadExcel() {
            const table = document.getElementById("stockTable");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Report" });
            XLSX.writeFile(wb, 'pole_plant_report.xlsx');
        }

        // Live search
        document.getElementById("searchInput").addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll("#stockTable tbody tr");

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>
</html>
