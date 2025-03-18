<div class="container mt-5 min-h-[400px]">

    <!-- Success Alert -->

    <div>

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.opacity.duration.500ms
                class="fixed top-4 left-1/2 transform -translate-x-1/2 w-96 flex items-center p-3 text-blue-800 border border-blue-300 rounded-lg bg-blue-50 shadow-lg dark:bg-gray-800 dark:text-blue-400 dark:border-blue-600">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <!-- PDF Upload Form -->

    <div class="w-full mx-auto max-w-screen-xl">

        <div>

            <form wire:submit.prevent="uploadPdf" class="mb-4 relative">
                {{-- <div class="mb-4">
                    <label for="pdfFile" class="block text-lg mb-2">Виберіть PDF файл</label>
                    <input type="file" id="pdfFile" wire:model="pdfFile" accept="application/pdf"
                        class="form-input block w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 @error('pdfFile') border-red-500 @enderror">

                    @error('pdfFile')
                        <div class="text-red-600 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                </div> --}}
                <div class="mb-4">
                    <label for="pdfFile"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-700">Виберіть PDF
                        файл</label>
                    <input type="file" id="pdfFile" wire:model="pdfFile" accept="application/pdf"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-50 dark:border-gray-600 dark:placeholder-gray-400">


                </div>

                @if ($pdfFile)
                    {{-- <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                        >
                        <div wire:loading wire:target="uploadPdf"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2">

                        </div>
                        <span class="ml-10">Прочитати PDF</span>
                    </button> --}}
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 inline-flex items-center">
                        <div wire:loading wire:target="uploadPdf">
                            <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin"
                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
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
            </form>
        </div>



        {{-- @if (isset($parsedData) && count($parsedData) > 0) --}}
        @if (is_array($parsedData) && count($parsedData) > 0)

            <!-- Data Table -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-100 dark:text-gray-100">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">№ п/п</th>
                            <th scope="col" class="px-6 py-3">Дата операції</th>
                            <th scope="col" class="px-6 py-3">% ставка</th>
                            <th scope="col" class="px-6 py-3">Операція</th>
                            <th scope="col" class="px-6 py-3">Сума</th>
                            <th scope="col" class="px-6 py-3">Сума в грн.</th>
                            <th scope="col" class="px-6 py-3">Призначення платежу</th>
                            <th scope="col" class="px-6 py-3">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parsedData as $index => $row)
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $row[0] }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($editedRowIndex === $index)
                                        <input type="text" wire:model="editedRowData.1"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-2 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    @else
                                        {{ $row[1] }}
                                    @endif
                                </td>

                                <td class="px-6 py-4 font-medium text-gray-900  dark:text-white">
                                    @if ($editedRowIndex === $index)
                                        <input type="text" wire:model="editedRowData.2"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-2 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    @else
                                        {{ $row[2] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900  dark:text-white">
                                    @if ($editedRowIndex === $index)
                                        <input type="text" wire:model="editedRowData.3"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-2 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    @else
                                        {{ $row[3] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-red-500  dark:text-yellow-300">

                                    @if ($editedRowIndex === $index)
                                        <input type="text" wire:model="editedRowData.4"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-2 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    @else
                                        {{ $row[4] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900  dark:text-white">
                                    @if ($editedRowIndex === $index)
                                        <input type="text" wire:model="editedRowData.5"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-2 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    @else
                                        {{ $row[5] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white">
                                    @if ($editedRowIndex === $index)
                                        <input type="text" wire:model="editedRowData.6"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-2 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    @else
                                        {{ $row[6] }}
                                    @endif
                                </td>

                                <td class="px-4 py-2 text-center" style="white-space: nowrap;">
                                    @if ($editedRowIndex === $index)
                                        <button wire:click="saveRow"
                                            class="icon-button text-green-500 mx-1 hover:scale-125" title="Save">
                                            <i class="fa-solid fa-floppy-disk fa-lg"></i>
                                        </button>
                                        <button wire:click="cancelEdit"
                                            class="icon-button text-gray-500 mx-1 hover:scale-125" title="Cancel">
                                            <i class="fa-solid fa-xmark fa-lg"></i>
                                        </button>
                                    @else
                                        <button wire:click="editRow({{ $index }})"
                                            class="icon-button text-blue-500 mx-1 hover:scale-125" title="Edit">
                                            <i class="fa-solid fa-pen fa-lg"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>








            <button type="button" wire:click="exportToExcel"
                class="btn bg-green-700 text-white px-4 py-2 mt-3 mr-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Експорт в Excel
            </button>
            <button wire:click="exportToDbf"
                class="btn bg-green-600 text-white px-4 py-2 mt-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Експорт в DBF
            </button>
        @endif

    </div>




    {{-- <style>
        .icon-hover {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .icon-hover:hover {
            transform: scale(1.2);
            color: #ff8c00 !important;
            cursor: pointer;
        }

        .icon-button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .icon-button:focus {
            outline: none;
        }

        .btn-fixed {
            min-width: 200px;
            position: relative;
        }

        .spinner-container {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style> --}}

</div>
