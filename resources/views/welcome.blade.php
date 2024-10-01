<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Página do Facebook</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex flex-col justify-center items-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-semibold text-center mb-6">Cadastrar Página do Facebook</h1>

            <!-- Exibir Mensagens de Sucesso -->
            @if (session('success'))
                <div class="mb-4 text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Exibir Mensagens de Erro -->
            @if ($errors->any())
                <div class="mb-4 text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/cadastrar-facebook" method="POST" class="space-y-6">
                @csrf <!-- Token CSRF para proteção -->

                <!-- ID da Página -->
                <div>
                    <label for="page_id" class="block text-sm font-medium text-gray-700">ID DA PÁGINA</label>
                    <input type="text" name="page_id" id="page_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="1934078505218253" required>
                </div>

                <!-- Enviar -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
