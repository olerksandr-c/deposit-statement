@section('title', 'Депозитна виписка')
<div class="container mt-5 px-5 mb-5 mx-auto w-full">
    <div class="w-full mx-auto max-w-screen-xl">
        <div>
            <form wire:submit.prevent="uploadPdf" class="mb-4 relative">

                <div class="mx-auto">
                    <label for="pdfFile" class="mb-1 block text-sm font-medium text-gray-700">Завантажити файл
                        pdf</label>
                    <input id="pdfFile" type="file" wire:model="pdfFile" accept="application/pdf"
                        class="block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-blue-500 file:py-2 file:px-4 file:text-sm file:font-semibold
                        file:text-white hover:file:bg-blue-700 focus:outline-none disabled:pointer-events-none disabled:opacity-60" />
                </div>


                {{-- @if ($pdfFile)
                    <div class="mt-2 text-sm text-gray-600">
                        Вибраний файл: {{ $pdfFile->getClientOriginalName() }} ({{ round($pdfFile->getSize() / 1024, 2) }} KB)
                    </div>
                @endif --}}



                <div class="mb-6">
                    <div class="flex items-center gap-3">
                        @error('pdfFile')
                            <span class="text-red-600 text-sm dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex space-x-2">
                    @if ($pdfFile && !$errors->has('pdfFile'))
                        <button type="submit"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center min-w-[200px] transition-colors duration-200 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-900">
                            <div wire:loading wire:target="uploadPdf" class="mr-2">
                                <svg aria-hidden="true" class="w-4 h-4 text-white animate-spin" viewBox="0 0 100 101"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="#E5E7EB" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                            Прочитати PDF
                        </button>
                    @endif

                    @if (is_array($parsedData) && count($parsedData) > 0)
                        <button type="button" wire:click="exportToExcel"
                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors duration-200 dark:bg-green-700 dark:hover:bg-green-800">
                            Експорт в Excel
                        </button>
                        <button wire:click="exportToDbf"
                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors duration-200 dark:bg-green-700 dark:hover:bg-green-800">
                            Експорт в DBF
                        </button>
                    @endif
                </div>
            </form>
        </div>

        @if (is_array($parsedData) && count($parsedData) > 0)
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full border-collapse border text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">№</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Дата операції</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Операція</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Сума</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Сума в грн.</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Призначення платежу</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Виписка сформована</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parsedData as $index => $row)
                            <tr
                                class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $index % 2 == 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }}">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap">
                                    {{ $row[0] ?? '' }}
                                </td>

                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.1"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.1') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.1')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ $row[1] ?? '' }}
                                    @endif
                                </td>

                                {{-- <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.2"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.2') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.2')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ $row[2] ?? '' }}
                                    @endif
                                </td> --}}

                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.3"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.3') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.3')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ $row[3] ?? '' }}
                                    @endif
                                </td>

                                <td
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap text-right">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.4"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.4') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.4')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ isset($row[4]) && is_numeric($row[4]) ? number_format($row[4], 2, ',', ' ') : $row[4] ?? '' }}
                                    @endif
                                </td>

                                <td
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap text-right">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.5"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-right dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.5') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.5')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ isset($row[5]) && is_numeric($row[5]) ? number_format($row[5], 2, ',', ' ') : $row[5] ?? '' }}
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-900 dark:text-gray-200">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.6"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.6') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.6')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ $row[6] ?? '' }}
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-900 dark:text-gray-200">
                                    @if ($editedRowIndex === $index)
                                        <div>
                                            <input type="text" wire:model.defer="editedRowData.7"
                                                class="w-full px-2 py-1 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('editedRowData.7') border-red-500 dark:border-red-400 @enderror">
                                            @error('editedRowData.7')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @else
                                        {{ $row[7] ?? '' }}
                                    @endif
                                </td>

                                <td class="px-4 py-2 text-center whitespace-nowrap">
                                    @if ($editedRowIndex === $index)
                                        <button wire:click="saveRow" wire:loading.attr="disabled"
                                            class="inline-flex items-center p-1.5 text-green-500 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 mx-1 hover:scale-125 transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="Save">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" />
                                            </svg>
                                        </button>
                                        <button wire:click="cancelEdit" wire:loading.attr="disabled"
                                            class="inline-flex items-center p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mx-1 hover:scale-125 transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="Cancel">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="editRow({{ $index }})"
                                            class="inline-flex items-center p-1.5 text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mx-1 hover:scale-125 transition-transform"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
