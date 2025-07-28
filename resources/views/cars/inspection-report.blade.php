<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inspection Report - {{ $car->model ?? 'Car' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .report-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        
        .logo {
            width: 200px;
            height: 120px;
            object-fit: contain;
        }
        
        .company-info {
            text-align: center;
            flex-grow: 1;
            margin: 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 14px;
            color: #666;
        }
        
        .report-title {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 30px 0;
            text-decoration: underline;
        }
        
        .customer-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 2px dashed #ccc;
            text-align: center;
        }
        
        .customer-form.no-print {
            display: block;
        }
        
        .form-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: 600;
            min-width: 120px;
            color: #333;
        }
        
        .form-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .print-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        .report-content {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed;
        }
        
        .report-row {
            display: table-row;
        }
        
        .report-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 12px;
        }
        
        .label {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            width: 10%;
            text-align: right;
            font-size: 10px;
        }
        
        .value {
            background: white;
            color: #333;
            width: 30%;
            font-size: 12px;
        }
        
        .car-info-grid {
            display: flex;
            gap: 0;
            margin-bottom: 20px;
            width: 100%;
            align-items: flex-start;
        }
        
        .car-info-grid .car-info-section {
            flex: 1;
            width: 50%;
        }
        
        .car-info-section {
            border: 1px solid #ddd;
            display: table;
            width: 100%;
            table-layout: fixed;
            min-width: 0;
            overflow: hidden;
            align-self: flex-start;
        }
        
        .car-info-section .report-row {
            display: table-row;
        }
        
        .car-info-section .report-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 12px;
        }
        
        .car-info-section .report-row:first-child .report-cell {
            background: #333;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 8px;
            font-size: 12px;
        }
        
        .section-title {
            background: #333;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 10px;
            font-size: 16px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .stamp {
            position: relative;
            display: inline-block;
            margin-top: 20px;
        }
        
        .stamp-circle {
            width: 100px;
            height: 100px;
            border: 3px solid #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc3545;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            line-height: 1.2;
        }
        
        @media print {
            .customer-form.no-print {
                display: none;
            }
            
            .report-container {
                box-shadow: none;
                padding: 10px;
            }
            
            .header .logo {
                width: 180px;
                height: 100px;
            }
            
            .company-name {
                font-size: 20px;
            }
            
            .company-details {
                font-size: 12px;
            }
            
            .report-title {
                font-size: 24px;
                margin: 20px 0;
            }
            
            .report-cell {
                padding: 8px;
                font-size: 12px;
            }
            
            .section-title {
                font-size: 14px;
                padding: 8px;
            }
            
            .stamp-circle {
                width: 80px;
                height: 80px;
                font-size: 10px;
            }
        }
        
        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Customer Name Input Form (Hidden on Print) -->
        <div class="customer-form no-print">
            <div class="form-group">
                <label class="form-label">اسم العميل:</label>
                <input type="text" id="customerNameInput" class="form-input" placeholder="أدخل اسم العميل">
                <button type="button" class="print-btn" onclick="printReport()">طباعة التقرير</button>
            </div>
        </div>

        <!-- Report Header -->
        <div class="header">
            <div style="width: 200px;"></div>
            <div class="company-info">
                <img src="{{ asset('assets/media/app/capMot.png') }}" alt="Captain Motors Logo" class="logo">
                <div class="company-details">الراي، قطعة 1 / مقابل البنك التجاري</div>
            </div>
            <div style="width: 200px;"></div>
        </div>

        <!-- Report Title -->
        <div class="report-title">تقرير فحص السيارة</div>

        <!-- Report Content -->
        <div class="report-content">
            <!-- Car Information in Two Columns -->
            <div class="car-info-grid">
                <div class="car-info-section">
                    <div class="report-row">
                        <div class="report-cell" colspan="2">معلومات السيارة</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">نوع السيارة:</div>
                        <div class="report-cell value">{{ $car->model ?? 'غير محدد' }}</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">اللون:</div>
                        <div class="report-cell value">{{ $car->color ?? 'غير محدد' }}</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">سنة الصنع:</div>
                        <div class="report-cell value">{{ $car->manufacturing_year ?? 'غير محدد' }}</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">عداد المسافات:</div>
                        <div class="report-cell value">{{ $car->odometer ? number_format($car->odometer) . ' كم' : 'غير محدد' }}</div>
                    </div>
                </div>
                
                <div class="car-info-section">
                    <div class="report-row">
                        <div class="report-cell" colspan="2">معلومات إضافية</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">رقم اللوحة:</div>
                        <div class="report-cell value">{{ $car->plate_number ?? 'غير محدد' }}</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">المحرك:</div>
                        <div class="report-cell value">{{ $car->engine_capacity ?? 'غير محدد' }}</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">اسم العميل:</div>
                        <div class="report-cell value" id="reportCustomerName">سيتم إدخاله قبل الطباعة</div>
                    </div>
                    
                    <div class="report-row">
                        <div class="report-cell label">التاريخ:</div>
                        <div class="report-cell value">{{ now()->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Inspection Results -->
            <div class="report-row">
                <div class="report-cell section-title" colspan="1">نتائج الفحص</div>
            </div>
            
            <div class="report-row">
                <div class="report-cell label">الشاصي:</div>
                <div class="report-cell value">{{ $car->inspection->chassis_inspection ?? 'غير محدد' }}</div>
            </div>
            
            <div class="report-row">
                <div class="report-cell label">المحرك:</div>
                <div class="report-cell value">{{ $car->inspection->motor ?? 'غير محدد' }}</div>
            </div>
            
            <div class="report-row">
                <div class="report-cell label">ناقل الحركة:</div>
                <div class="report-cell value">{{ $car->inspection->transmission ?? 'غير محدد' }}</div>
            </div>
            
            <div class="report-row">
                <div class="report-cell label">الهيكل:</div>
                <div class="report-cell value">
                    @if ($car->inspection)
                        @php
                            $carParts = [
                                'hood' => 'غطاء المحرك',
                                'front_right_fender' => 'المجنب الأمامي الأيمن',
                                'front_left_fender' => 'المجنب الأمامي الأيسر',
                                'rear_right_fender' => 'المجنب الخلفي الأيمن',
                                'rear_left_fender' => 'المجنب الخلفي الأيسر',
                                'trunk_door' => 'باب الصندوق',
                                'front_right_door' => 'الباب الأمامي الأيمن',
                                'rear_right_door' => 'الباب الخلفي الأيمن',
                                'front_left_door' => 'الباب الأمامي الأيسر',
                                'rear_left_door' => 'الباب الخلفي الأيسر'
                            ];
                            $bodyInspection = [];
                            foreach ($carParts as $field => $label) {
                                if ($car->inspection->$field) {
                                    $bodyInspection[] = $label . ': ' . \App\Models\CarInspection::getInspectionDisplayName($car->inspection->$field);
                                }
                            }
                        @endphp
                        {{ !empty($bodyInspection) ? implode('، ', $bodyInspection) : 'غير محدد' }}
                    @else
                        غير محدد
                    @endif
                </div>
            </div>
            
            <div class="report-row">
                <div class="report-cell label">الملاحظات:</div>
                <div class="report-cell value">{{ $car->inspection->body_notes ?? 'لا توجد ملاحظات' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="stamp">
                <div class="stamp-circle">
                    تم الفحص<br>والتأكد<br>من الحالة
                </div>
            </div>
        </div>
    </div>

    <script>
        function printReport() {
            const customerName = document.getElementById('customerNameInput').value.trim();
            if (!customerName) {
                alert('يرجى إدخال اسم العميل قبل الطباعة');
                return;
            }
            
            document.getElementById('reportCustomerName').textContent = customerName;
            
            setTimeout(() => {
                window.print();
            }, 100);
        }
    </script>
</body>
</html> 