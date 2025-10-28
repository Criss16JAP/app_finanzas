<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ type: '{{ old('type', 'Bank') }}' }"> <!-- Alpine.js x-data -->

                    <!-- ... (formulario y errores) ... -->
                    <form action="{{ route('cuentas.store') }}" method="POST">
                        @csrf

                        <!-- Nombre -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Nombre') }}</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Tipo -->
                        <div class="mt-4">
                            <label for="type" class="block font-medium text-sm text-gray-700">{{ __('Tipo de Cuenta') }}</label>
                            <select id="type" name="type" required x-model="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="Bank">{{ __('Cuenta Bancaria') }}</option>
                                <option value="Cash">{{ __('Efectivo') }}</option>
                                <option value="ManualCreditCard">{{ __('Tarjeta de Crédito (Manual)') }}</option>
                            </select>
                        </div>

                        <!-- Saldo Inicial (REGLA 4) -->
                        <div class="mt-4" x-show="type !== 'ManualCreditCard'">
                            <label for="balance" class="block font-medium text-sm text-gray-700">{{ __('Saldo Inicial') }}</slabel>
                            <input id="balance" name="balance" type="number" step="0.01" value="{{ old('balance', 0) }}"
                                   :disabled="type === 'ManualCreditCard'"
                                   class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Las tarjetas de crédito siempre inician en $0.</p>
                        </div>

                        <!-- ... (botones) ... -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
                            <button type="submit" class="px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                                {{ __('Guardar Cuenta') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
