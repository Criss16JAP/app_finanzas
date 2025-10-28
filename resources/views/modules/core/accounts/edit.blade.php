<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cuenta') }}: {{ $account->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- ... (errores) ... -->
                    <form action="{{ route('cuentas.update', $account) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Nombre -->
                        <div>
                            <label for="name"
                                class="block font-medium text-sm text-gray-700">{{ __('Nombre') }}</label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name', $account->name) }}" required
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Tipo (Deshabilitado, no se puede cambiar) -->
                        <div class="mt-4">
                            <label for="type"
                                class="block font-medium text-sm text-gray-700">{{ __('Tipo de Cuenta') }}</label>
                            <input id="type" name="type_display" type="text" value="{{ $account->type }}"
                                disabled class="block mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            <!-- Input oculto para validación (si es necesario) -->
                            <input type="hidden" name="type" value="{{ $account->type }}">
                        </div>

                        <!-- Saldo (REGLA 4) -->
                        <div class="mt-4">
                            <label for="balance"
                                class="block font-medium text-sm text-gray-700">{{ __('Saldo Actual') }}</slabel>
                                <input id="balance" name="balance" type="number" step="0.01"
                                    value="{{ old('balance', $account->balance) }}"
                                    @if ($account->type == 'ManualCreditCard') readonly @else required @endif
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm @if ($account->type == 'ManualCreditCard') bg-gray-100 @endif">
                                @if ($account->type == 'ManualCreditCard')
                                    <p class="mt-1 text-xs text-gray-500">El saldo de las tarjetas solo se puede
                                        modificar mediante transacciones.</p>
                                @endif
                        </div>

                        @if ($account->type == 'Bank')
                            <div class="mt-4">
                                <label for="is_exempt_4x1000" class="flex items-center">
                                    <input id="is_exempt_4x1000" name="is_exempt_4x1000" type="checkbox" value="1"
                                        @if (old('is_exempt_4x1000', $account->is_exempt_4x1000)) checked @endif
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span
                                        class="ml-2 text-sm text-gray-600">{{ __('Marcar como cuenta exenta del 4x1000') }}</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Solo puedes tener una cuenta exenta a la vez.
                                    Marcar esta desmarcará cualquier otra.</p>
                            </div>
                        @endif

                        <!-- ... (botones) ... -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('cuentas.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
                            <button type="submit"
                                class="px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                                {{ __('Actualizar Cuenta') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
