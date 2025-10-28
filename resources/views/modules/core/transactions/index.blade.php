<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Transacciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Mensajes de Sesión -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Botón para Crear Nueva -->
                    <div class="mb-4">
                        <a href="{{ route('transacciones.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Registrar Movimiento') }}
                        </a>
                    </div>

                    <!-- Tabla de Transacciones -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalle
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuenta
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                        Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <!-- Fecha -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->transaction_date->format('Y-m-d') }}
                                        </td>

                                        <!-- Detalle -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->description ?: 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if ($transaction->related_account_id && $transaction->type == 'Ingreso')
                                                    (Desde: {{ $transaction->relatedAccount->name ?? 'N/A' }})
                                                @elseif ($transaction->related_account_id && $transaction->type == 'Egreso')
                                                    (Para: {{ $transaction->relatedAccount->name ?? 'N/A' }})
                                                @else
                                                    {{ $transaction->category->name ?? 'Sin Categoría' }}
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Cuenta -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->account->name }}
                                        </td>

                                        <!-- Monto -->
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium
                                            @if ($transaction->type == 'Ingreso') text-green-600 @else text-red-600 @endif">
                                            {{ $transaction->type == 'Ingreso' ? '+' : '-' }}
                                            ${{ number_format($transaction->amount, 2) }}
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                            @if (!$transaction->parent_transaction_id && optional($transaction->category)->name != 'Impuestos')
                                                <a href="{{ route('transacciones.edit', $transaction) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                                <form action="{{ route('transacciones.destroy', $transaction) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('¿Estás seguro? Esto recalculará el saldo de tu cuenta.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 ml-4">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 italic">N/A</span>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No hay transacciones registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
