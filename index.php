<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login SSO - NewFS</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .btn-info {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 600;
            display: block;
            width: 100%;
            text-align: center;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }
        .btn-info:hover {
            background-color: #2563eb;
            color: white;
            text-decoration: none;
        }
        .login-page {
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .logo {
            width: 50%;
            margin: 0 auto 1.5rem auto;
            display: block;
        }
        h4 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        .text-center a {
            color: #3b82f6;
            font-weight: 600;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-gray-100 font-['Roboto'],sans-serif min-h-screen flex flex-col items-center justify-center p-6">
    <div class="login-page">
        <div class="form-group" style="text-align:center">
            <h1><strong>Politeknik Negeri Banyuwangi</strong></h1>
        </div>
        <div class="row" style="display: flex; justify-content: center;">
            <img src="./images/logo_poliwangi.png" alt="Politeknik Negeri Banyuwangi" class="logo" />
        </div>
        <div class="form-group" style="text-align:center">
            <h4>New File System</h4>
        </div>
        <form method="post" class="clearfix">
            <div class="form-group">
                <a href="http://localhost:8888/sso.php" class="btn-info">Login SSO</a>
            </div>
            <div class="text-center">
                <a href="https://sso.poliwangi.ac.id/password/reset">Lupa password</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("contextmenu", function(e){
            e.preventDefault();
        }, false);
    </script>
</body>
</html>
