<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Training Complete</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <div class="mx-auto flex min-h-screen max-w-3xl items-center justify-center p-6">
        <div class="w-full rounded-2xl bg-white p-8 text-center shadow">
            <h1 class="text-3xl font-bold text-emerald-600">„»«—ﬂ!  „ ≈‰Â«¡ «· œ—Ì» »‰Ã«Õ</h1>
            <p class="mt-3 text-slate-600">«·‰ ÌÃ… «·‰Â«∆Ì… ··ÿ«·»:</p>
            <p class="mt-2 text-5xl font-extrabold text-slate-800">{{ $application->final_score ?? 0 }}/100</p>
            <p class="mt-2 text-sm text-slate-500">Application #{{ $application->id }}</p>
            <a href="{{ route('board') }}" class="mt-6 inline-block rounded bg-sky-600 px-5 py-2 text-white hover:bg-sky-700">«·—ÃÊ⁄ ··ÊÕ…</a>
        </div>
    </div>
</body>
</html>
