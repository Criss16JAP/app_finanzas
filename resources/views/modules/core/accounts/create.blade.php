<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cuentas.store') }}" method="POST">
                        @csrf

                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Nombre') }}</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div class="mt-4">
                            <label for="type" class="block font-medium text-sm text-gray-700">{{ __('Tipo de Cuenta') }}</label>
                            <select id="type" name="type" required
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Selecciona un tipo') }}</option>
                                <option value="Bank" {{ old('type') == 'Bank' ? 'selected' : '' }}>{{ __('Cuenta Bancaria') }}</option>
                                <option value="Cash" {{ old('type') == 'Cash' ? 'selected' : '' }}>{{ __('Efectivo') }}</option>
                                <option value="ManualCreditCard" {{ old('type') == 'ManualCreditCard' ? 'selected' : '' }}>{{ __('Tarjeta de Crédito (Manual)') }}</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="balance" class="block font-medium text-sm text-gray-700">{{ __('Saldo Inicial (Opcional)') }}</slabel>
                            <input id="balance" name="balance" type="number" step="0.01" value="{{ old('balance', 0) }}"
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Para tarjetas de crédito, puedes poner el saldo negativo (ej: -500).</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Guardar Cuenta') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
