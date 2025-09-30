<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Policy Created</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg,rgb(255, 255, 255) 0%, #bf0022 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .policy-info {
            background-color: #f8f9fa;
            border-left: 4px solid #bf0022;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .policy-info h3 {
            margin: 0 0 15px 0;
            color: #bf0022;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-draft {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-submitted {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #bf0022;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #bf0022;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>New Policy Created</h1>
            <p>A new policy has been created</p>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            
            <p>A new policy has been created and requires your attention. Please review the details below:</p>
            
            <div class="policy-info">
                <h3>Policy Information</h3>
                <div class="info-row">
                    <span class="info-label">Policy Number:</span>
                    <span class="info-value">{{ $policy->policy_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower($policy->status) }}">
                            {{ $policy->status }}
                        </span>
                    </span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('cases.view', encrypt($policy->id)) }}" class="btn">View Policy</a>
            </div>
            
        </div>
        
        <div class="footer">

        </div>
    </div>
</body>
</html>
