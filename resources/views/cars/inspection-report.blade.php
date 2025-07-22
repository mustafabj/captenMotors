<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير معاينة المركبة</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');

        @page {
            margin: 15mm;
            size: A4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Arial', sans-serif;
            font-size: 16px;
            direction: rtl;
            padding: 20px;
            background: #fff;
            border: 2px solid #000;
            border-radius: 8px;
            margin: 10px;
            line-height: 1.5;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header .logo {
            width: 300px;        
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

                .header .info-arabic,
        .header .info-english {
            font-size: 13px;
            line-height: 1.3;
            font-weight: 500;
            flex: 1;
            max-width: 180px;
        }
        
        .info-arabic {
            text-align: right;
        }
        
        .info-english {
            text-align: left;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            border: 2px solid #4a5568;
            border-radius: 8px;
            background: #fafafa;
        }

        .section h3 {
            margin-bottom: 15px;
            font-size: 20px;
            font-weight: 700;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 8px;
            color: #4a5568;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td {
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #4a5568;
            vertical-align: middle;
        }

        .info-table td,
        .notes-table td {
            border: 1px solid #4a5568;
        }

        .label {
            font-weight: 600;
            width: 35%;
            background-color: #e2e8f0;
            color: #2d3748;
            text-align: center;
        }

        .value {
            width: 65%;
            background-color: #fff;
            text-align: center;
            font-weight: 500;
        }

        .notes-table .label {
            width: 25%;
        }

        .notes-table .value {
            width: 75%;
        }

        .chassis-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 8px;
        }

        .chassis-item {
            background: #f7fafc;
            padding: 8px 10px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 14px;
        }

        .chassis-item strong {
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
        }

        .stamp {
            border: 3px solid #4a5568;
            border-radius: 8px;
            padding: 20px;
            display: inline-block;
            text-align: center;
            margin-bottom: 25px;
            background: white;
            min-width: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stamp-title {
            font-size: 18px;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .stamp-text {
            font-size: 14px;
            color: #000;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .signature-line {
            margin-top: 25px;
            border-top: 2px solid #4a5568;
            padding-top: 15px;
            text-align: center;
            font-size: 14px;
            color: #555;
            font-weight: 500;
        }

        .customer-form {
            background: #f7fafc;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #cbd5e0;
        }

        .customer-form h3 {
            margin-top: 0;
            color: #4a5568;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 16px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #cbd5e0;
            border-radius: 6px;
            font-size: 16px;
            font-family: 'Cairo', 'Arial', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #4a5568;
            box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.1);
        }

        .print-button {
            background: #4a5568;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Cairo', 'Arial', sans-serif;
        }

        .print-button:hover {
            background: #2d3748;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                font-size: 12px;
                border: none;
                margin: 0;
                padding: 5px;
                line-height: 1.2;
            }
            
            .header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }
            
            .header .logo {
                width: 250px;
            }
            
            .header .logo img {
                max-width: 100%;
            }
            
            .header .info-arabic,
            .header .info-english {
                font-size: 12px;
                line-height: 1.3;
            }
            
            .section {
                margin-bottom: 15px;
                padding: 10px;
                background: white;
                border: 1px solid #000;
            }
            
            .section h3 {
                margin-bottom: 8px;
                font-size: 14px;
                padding-bottom: 5px;
            }
            
            td {
                padding: 6px 8px;
                font-size: 11px;
            }
            
            .label {
                background-color: #f0f0f0 !important;
                width: 30%;
            }
            
            .value {
                background-color: white !important;
                width: 70%;
            }
            
            .notes-table .label {
                width: 20%;
            }
            
            .notes-table .value {
                width: 80%;
            }
            
            .chassis-grid {
                gap: 4px;
                margin-top: 4px;
            }
            
            .chassis-item {
                padding: 4px 6px;
                font-size: 10px;
            }
            
            .chassis-item strong {
                font-size: 10px;
            }
            
            .footer {
                margin-top: 15px;
                padding-top: 10px;
            }
            
            .stamp {
                padding: 12px;
                margin-bottom: 15px;
                min-width: 150px;
            }
            
            .stamp-title {
                font-size: 14px;
                margin-bottom: 5px;
            }
            
            .stamp-text {
                font-size: 11px;
                margin-bottom: 3px;
            }
            
            .signature-line {
                margin-top: 15px;
                padding-top: 8px;
                font-size: 11px;
            }
            
            @page {
                margin: 10mm;
                size: A4;
            }
        }
        
    </style>
</head>

<body>
    <!-- Customer Information Form (Hidden when printing) -->
    <div class="customer-form no-print">
        <h3>إدخال معلومات العميل</h3>
        <div class="form-group">
            <label class="form-label">اسم العميل:</label>
            <input type="text" id="customerName" class="form-input" placeholder="أدخل اسم العميل الكامل">
        </div>
        <div style="text-align: center;">
            <button type="button" class="print-button" onclick="printReport()">
                طباعة تقرير المعاينة
            </button>
        </div>
    </div>

    <!-- Report Content -->
    <div id="reportContent">
        <div class="header">
            <div class="info-arabic">
                <div style="font-size: 14px; color: #4a5568;">الراي، قطعة 1 شارع البنك التجاري، تلفون: ٥٠٠٠٠٠٠١</div>
            </div>
            <div class="logo">
                <img src="{{ asset('assets/media/app/capMot.png') }}" alt="Captain Motors Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="info-english">
                <div style="font-size: 14px; color: #4a5568;">Al-Rai, Plot 1 / In front of the Commercial Bank, Tel.: 50000001</div>
            </div>
        </div>

        <div class="section">
            <h3>معلومات السيارة</h3>
            <table class="info-table">
                <tr>
                    <td class="label">اسم العميل:</td>
                    <td class="value" id="reportCustomerName">-</td>
                </tr>
                <tr>
                    <td class="label">نوع السيارة:</td>
                    <td class="value">{{ $car->model ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <td class="label">اللون:</td>
                    <td class="value">{{ $car->color ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <td class="label">سنة الصنع:</td>
                    <td class="value">{{ $car->manufacturing_year ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <td class="label">رقم العداد:</td>
                    <td class="value">{{ $car->odometer ?? 'غير محدد' }} كم</td>
                </tr>
                <tr>
                    <td class="label">رقم اللوحة:</td>
                    <td class="value">{{ $car->plate_number ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <td class="label">المبلغ الإجمالي:</td>
                    <td class="value">50 دينار (كعربون)</td>
                </tr>
                <tr>
                    <td class="label">تاريخ المعاينة:</td>
                    <td class="value">{{ now()->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">وقت المعاينة:</td>
                    <td class="value">{{ now()->format('H:i:s') }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>ملاحظات الفحص</h3>
            <table class="notes-table">
                <tr>
                    <td class="label">المحرك:</td>
                    <td class="value">{{ $car->inspection->motor ?? 'لم يتم الفحص' }}</td>
                </tr>
                <tr>
                    <td class="label">ناقل الحركة:</td>
                    <td class="value">{{ $car->inspection->transmission ?? 'لم يتم الفحص' }}</td>
                </tr>
                <tr>
                    <td class="label">الهيكل:</td>
                    <td class="value">
                        @if ($car->inspection)
                            غطاء المحرك: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->hood ?? 'clean_and_free_of_filler') }}، 
                            المجنب الأمامي الأيمن: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->front_right_fender ?? 'clean_and_free_of_filler') }}، 
                            المجنب الأمامي الأيسر: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->front_left_fender ?? 'clean_and_free_of_filler') }}، 
                            المجنب الخلفي الأيمن: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->rear_right_fender ?? 'clean_and_free_of_filler') }}، 
                            المجنب الخلفي الأيسر: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->rear_left_fender ?? 'clean_and_free_of_filler') }}، 
                            باب الصندوق: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->trunk_door ?? 'clean_and_free_of_filler') }}، 
                            الباب الأمامي الأيمن: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->front_right_door ?? 'clean_and_free_of_filler') }}، 
                            الباب الخلفي الأيمن: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->rear_right_door ?? 'clean_and_free_of_filler') }}، 
                            الباب الأمامي الأيسر: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->front_left_door ?? 'clean_and_free_of_filler') }}، 
                            الباب الخلفي الأيسر: {{ \App\Models\CarInspection::getInspectionDisplayName($car->inspection->rear_left_door ?? 'clean_and_free_of_filler') }}
                        @else
                            لم يتم الفحص
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">الهيكل:</td>
                    <td class="value">{{ $car->inspection->body_notes ?? 'لم يتم الفحص' }}</td>
                </tr>
                <tr>
                    <td class="label">ملاحظات عامة:</td>
                    <td class="value">{{ $car->inspection->body_notes ?? 'لا توجد ملاحظات إضافية' }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">

            <div class="signature-line">
                العميل بأنه موافق على الشروط والتعليمات المسجلة خلف الفاتورة تقرير معاينة المركبة. توقيع<br>
                <small>The customer agrees to the terms and instructions recorded on the back of the invoice, vehicle
                    inspection report. Signature</small>
            </div>
        </div>
    </div>

    <script>
        function printReport() {
            const customerName = document.getElementById('customerName').value.trim();

            if (!customerName) {
                alert('يرجى إدخال اسم العميل قبل الطباعة.');
                return;
            }

            // Update the customer name in the report
            document.getElementById('reportCustomerName').textContent = customerName;

            // Show the report content
            document.getElementById('reportContent').style.display = 'block';

            // Print the report
            window.print();

            // Hide the report content after printing (optional)
            setTimeout(() => {
                document.getElementById('reportContent').style.display = 'none';
            }, 1000);
        }
    </script>
</body>

</html>
