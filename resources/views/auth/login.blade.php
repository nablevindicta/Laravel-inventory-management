<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Inventory</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <meta name="msapplication-TileColor" content="#206bc4" />
    <meta name="theme-color" content="#206bc4" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon" />
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    <style>
        /* CSS Kustom untuk tampilan yang lebih menarik */
        body {
            background: #f0f3f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #556B2F 0%, #15457a 100%);
        }

        .login-card-wrapper {
            max-width: 900px;
            width: 100%;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-image-side {
            background-image: url('https://source.unsplash.com/random/800x600?warehouse');
            background-size: cover;
            background-position: center;
        }

        .login-form-side {
            padding: 40px;
        }

        .login-form-side h3 {
            font-size: 2rem;
            color: #333;
            font-weight: 700;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card-wrapper d-flex">
            <div class="login-image-side d-none d-md-block col-6">
                </div>
            <div class="login-form-side col-md-6 col-12">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="text-center mb-4">
                        <img src="{{ asset('logo.png') }}" alt="logo" height="40" class="mb-2">
                        <h3 class="mb-3 font-weight-medium">
                            Selamat Datang
                        </h3>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="masukan email anda" name="email">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="masukan kata sandi anda" name="password">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>