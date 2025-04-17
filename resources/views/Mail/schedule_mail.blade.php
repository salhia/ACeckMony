<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #0a012e;
            color: #ffffff;
            text-align: center;
            padding: 40px 20px;
        }

        .header h1 {
            font-size: 36px;
            margin: 0;
            font-weight: 700;
        }

        .content {
            padding: 30px 20px;
            color: #333333;
            line-height: 1.6;
        }

        .content h3 {
            color: #0a012e;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 30px;
            background-color: #3c3c3c;
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            background-color: #0a012e;
            color: #fafafa;
            text-align: center;
            padding: 20px;
        }

        .footer p {
            margin: 5px 0;
            font-size: 14px;
        }

        .footer a {
            color: #fafafa;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>EMPOTech RealEstate</h1>
        </div>
        <div class="content">
            <p>Featuring the country's most selective developments, we promise investors and buyers an unmatched level of service.</p>
            <p><strong>Hello Sir,</strong><br>Your request is confirmed on this date.</p>
            <h3>Tour Date: {{ $schedule['tour_date'] }}</h3>
            <h3>Tour Time: {{ $schedule['tour_time'] }}</h3>
            <p>In 2024, NRE has re-imagined its brand with a new logo, mission, and vision to reaffirm its commitment to bringing innovation. The new identity reflects the company’s dedication to staying at the forefront of the real estate sector by embracing cutting-edge technologies and eco-friendly designs.</p>
            <p>The latest tagline, “BROADEN LIFE BOUNDARIES”, encapsulates a commitment to improving lifestyles and pushing the limits of living. This boldly refreshed brand identity articulates a forward-looking approach, ensuring that NRE continues to shape the future of real estate.</p>
            <a href="http://www.tech.empobd.com" class="button">READ MORE</a>
        </div>
        <div class="footer">
            <p><strong>About Us</strong></p>
            <p>At EMPOTech RealEstate, we are dedicated to providing exceptional real estate services, offering a wide range of properties that cater to diverse needs and preferences. Our team is committed to guiding you through every step of the buying or investing process.</p>
            <p><strong>Contact Us</strong></p>
            <p>123 Real Estate Avenue, Suite 456, Cityville</p>
            <p>Email: contact@empotechrealestate.com</p>
            <p>Phone: (+1) 123-456-7890</p>
            <p>2024 © EMPOTech RealEstate. All Rights Reserved.</p>
        </div>
    </div>
</body>

</html>
