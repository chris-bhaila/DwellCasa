<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DwellCasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-96 bg-slate-900 rounded-b-[4rem] z-0"></div>
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=1920')] opacity-10 mix-blend-overlay z-0"></div>

    <div class="relative z-10 max-w-md w-full mx-4">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-serif font-bold italic text-white tracking-wide">DwellCasa</h1>
            <p class="text-slate-300 mt-2 font-medium tracking-widest uppercase text-sm">Admin Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-8 md:p-10">
            <h2 class="text-2xl font-serif font-bold text-slate-900 mb-8 text-center italic">Welcome Back</h2>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6">
                    <div class="flex">
                        <div class="ml-3 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="mb-5">
                    <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@dwellcasa.com"
                        class="w-full px-4 py-3 text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#A89070] focus:border-transparent transition-all">
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Password</label>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#A89070] focus:border-transparent transition-all">
                </div>

                <button type="submit" class="w-full bg-[#A89070] text-white text-sm font-bold py-4 px-6 rounded-xl hover:bg-[#8E795E] transition-all transform hover:-translate-y-0.5 shadow-lg">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
        
        <p class="text-center text-slate-400 text-sm mt-8 font-medium tracking-wide">
            &copy; {{ date('Y') }} DwellCasa. All rights reserved.
        </p>
    </div>
</body>
</html>
