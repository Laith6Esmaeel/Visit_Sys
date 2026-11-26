<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بطاقة زائر - {{ $visit->badge_number }}</title>
    <style>
        @page {
            size: 80mm 100mm;
            margin: 3mm;
        }

        body {
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .badge {
            width: 74mm;
            background: #ffffff;
            border: 1.5px solid #000;
            border-radius: 6px;
            padding: 8px;
            box-sizing: border-box;
            text-align: center;
        }

        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header h2 {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
        }

        .header p {
            margin: 2px 0 0;
            font-size: 11px;
        }

        .badge-num {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            margin: 4px 0 8px;
            direction: ltr;
        }

        .details {
            text-align: right;
            font-size: 11px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .details strong {
            font-weight: 800;
        }

        .qr-box {
            display: flex;
            justify-content: center;
            margin: 6px 0;
        }

        .footer {
            font-size: 9px;
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        .actions {
            margin-top: 16px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-print { background: #005a36; color: #fff; }
        .btn-back { background: #334155; color: #fff; }

        @media print {
            body { background: #fff; min-height: auto; display: block; }
            .actions { display: none !important; }
            .badge { border: none; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="badge">
        <div class="header">
            <h2>الهيئة العامة للضرائب والرسوم</h2>
            <p>بطاقة دخول زائر</p>
        </div>

        <div class="badge-num">{{ $visit->badge_number }}</div>

        <div class="details">
            <div><strong>الاسم:</strong> {{ $visit->visitor?->full_name }}</div>
            <div><strong>المديرية:</strong> {{ $visit->directorate?->name }}</div>
            <div><strong>الغرفة:</strong> {{ $visit->room?->room_number }} ({{ $visit->room?->department?->name }})</div>
            <div><strong>السبب:</strong> {{ $visit->visit_reason }}</div>
            <div><strong>الدخول:</strong> {{ $visit->check_in?->format('Y-m-d H:i') }}</div>
        </div>

        <div class="qr-box">
            {!! $qrSvg !!}
        </div>

        <div class="footer">
            يرجى تسليم البطاقة للاستقبال عند المغادرة
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-print" onclick="window.print()">طباعة البطاقة</button>
        <a class="btn btn-back" href="{{ url('/admin/visit-records') }}">العودة لسجل الزيارات</a>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>