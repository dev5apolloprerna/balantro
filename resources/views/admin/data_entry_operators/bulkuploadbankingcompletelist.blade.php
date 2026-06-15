@extends('layouts.super_admin')
@section('content')

<div data-controller="confirm-delete" x-data="{ openLedger:false }">

    <div class="container mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold text-gray-800 dark:text-white">
                {{ __("Bank Statement") }}
            </h6>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">

                <div class="card rounded-lg overflow-hidden bg-white dark:bg-neutral-800 shadow-sm">


                    <!-- HEADER -->
                    <div class="bg-gray-50 dark:bg-neutral-700 border-b border-gray-200 dark:border-neutral-700 px-2 py-1">


                        <!-- TOP ROW -->
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">


                            <div class="flex items-center gap-3">

                                <a href="javascript:void(0)" id="backBtn" class="text-gray-600 dark:text-gray-300 text-lg">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>

                                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                                    Bank Transactions
                                </h2>

                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                    58
                                </span>

                                <i class="fa-solid fa-circle-info text-gray-400"></i>

                            </div>


                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    Company Name:
                                    <span class="font-semibold text-blue-600">
                                        Suvit (100000)
                                    </span>
                                </span>
                            </div>

                        </div>


                        <!-- SECOND ROW -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">


                            <!-- BULK -->
                            <div>

                                <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    Bulk Operations
                                    <i class="fa-solid fa-circle-info text-gray-400 text-xs"></i>
                                </div>

                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                    Update Bulk Records:
                                </div>

                                <select class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-gray-700 dark:text-gray-200 rounded-md w-full px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">

                                    <option>Select Column</option>
                                    <option>Voucher Type</option>
                                    <option>Party Name</option>
                                    <option>Place of Supply</option>

                                </select>

                            </div>


                            <!-- FILTERS -->
                            <div>

                                <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    General Filters
                                    <i class="fa-solid fa-circle-info text-gray-400 text-xs"></i>
                                </div>

                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-300 mb-3">

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox">
                                        Hide Tally Synced Records
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox">
                                        Saved Records
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox">
                                        Blank Records
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox">
                                        Failed Records
                                    </label>

                                </div>


                                <div class="flex items-center gap-2 text-sm">

                                    <span class="text-gray-700 dark:text-gray-300">Date:</span>

                                    <input type="date"
                                        class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-sm">

                                    <input type="date"
                                        class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-sm">

                                </div>

                            </div>


                            <!-- ACTIONS -->
                            <div class="flex items-start justify-end gap-3">


                                <button
                                    @click="openLedger=true"
                                    class="border border-blue-500 text-blue-600 dark:text-blue-400 px-3 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-neutral-700">

                                    <i class="fa-solid fa-plus"></i>
                                    Create Ledger

                                </button>


                                <button
                                    class="bg-blue-600 text-white px-2 py-1 rounded-md flex items-center gap-2 hover:bg-blue-700">

                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Save

                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- TABLE -->
                    <div class="overflow-x-auto p-4">

                        <table class="min-w-full text-sm text-gray-700 dark:text-gray-200">

                            <thead class="bg-gray-200 dark:bg-neutral-700 text-xs uppercase">

                                <tr>

                                    <th class="px-3 py-3"><input type="checkbox"></th>

                                    <th class="px-3 py-3">Sr. No.</th>

                                    <th class="px-3 py-3">Date</th>

                                    <th class="px-3 py-3">Description</th>

                                    <th class="px-3 py-3">Type</th>

                                    <th class="px-3 py-3 text-right">Amount</th>

                                    <th class="px-3 py-3">Ledger</th>

                                    <th class="px-3 py-3 text-center">Actions</th>

                                </tr>

                                <!-- FILTER ROW -->

                                <tr class="bg-white dark:bg-neutral-800">

                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th class="px-2 pb-2">
                                        <input type="text"
                                            class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md w-full px-3 py-2 text-xs"
                                            placeholder="Search">
                                    </th>

                                    <th class="px-2 pb-2">
                                        <input type="text"
                                            class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md w-full px-3 py-2 text-xs"
                                            placeholder="Search">
                                    </th>

                                    <th class="px-2 pb-2 flex gap-2">
                                        <input type="text"
                                            class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md w-full px-2 py-2 text-xs"
                                            placeholder="From">

                                        <input type="text"
                                            class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md w-full px-2 py-2 text-xs"
                                            placeholder="To">
                                    </th>

                                    <th class="px-2 pb-2">
                                        <input type="text"
                                            class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md w-full px-3 py-2 text-xs"
                                            placeholder="Search Ledger">
                                    </th>

                                    <th></th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">


                                <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">

                                    <td class="px-3 py-3"><input type="checkbox"></td>

                                    <td class="px-3 py-3">1</td>

                                    <td class="px-3 py-3">12/01/2021</td>

                                    <td class="px-3 py-3 text-gray-600">
                                        ACH Credit / AACHH7685E-AY2020
                                    </td>

                                    <td class="px-3 py-3">

                                        <select class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md px-2 py-1 text-xs">

                                            <option>Receipt</option>
                                            <option>Payment</option>

                                        </select>

                                    </td>

                                    <td class="px-3 py-3 text-right font-medium">
                                        25770
                                    </td>

                                    <td class="px-3 py-3">

                                        <select class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md px-2 py-1 text-xs w-full">

                                            <option>Select Ledger</option>

                                        </select>

                                    </td>

                                    <td class="px-3 py-3 flex justify-center gap-3 text-gray-500">

                                        <i class="fa-solid fa-circle-plus cursor-pointer"></i>

                                        <i class="fa-solid fa-copy cursor-pointer"></i>

                                        <i class="fa-solid fa-trash text-red-500 cursor-pointer"></i>

                                    </td>

                                </tr>


                                <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">

                                    <td class="px-3 py-3"><input type="checkbox"></td>

                                    <td class="px-3 py-3">2</td>

                                    <td class="px-3 py-3">05/01/2021</td>

                                    <td class="px-3 py-3 text-gray-600">
                                        NEFT-IBARB121005608884
                                    </td>

                                    <td class="px-3 py-3">

                                        <select class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md px-2 py-1 text-xs">

                                            <option>Payment</option>
                                            <option>Receipt</option>

                                        </select>

                                    </td>

                                    <td class="px-3 py-3 text-right font-medium">
                                        40000
                                    </td>

                                    <td class="px-3 py-3">

                                        <select class="border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-md px-2 py-1 text-xs w-full">

                                            <option>Select Ledger</option>

                                        </select>

                                    </td>

                                    <td class="px-3 py-3 flex justify-center gap-3 text-gray-500">

                                        <i class="fa-solid fa-circle-plus cursor-pointer"></i>

                                        <i class="fa-solid fa-copy cursor-pointer"></i>

                                        <i class="fa-solid fa-trash text-red-500 cursor-pointer"></i>

                                    </td>

                                </tr>


                            </tbody>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- MODAL -->
    <div x-show="openLedger"
        x-transition
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">


        <div class="bg-white dark:bg-neutral-800 w-[1050px] rounded-md shadow-lg">


            <div class="flex justify-between items-center border-b border-gray-200 dark:border-neutral-700 px-5 py-3 bg-gray-100 dark:bg-neutral-700">

                <h2 class="text-gray-700 dark:text-gray-200 font-semibold text-sm">
                    Add Ledger For Suvit (100000)
                </h2>

                <button @click="openLedger=false">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>

            </div>


            <div class="p-5 space-y-4 text-gray-700 dark:text-gray-200">

                <!-- FORM CONTENT SAME AS YOUR CODE -->
                <!-- Only input classes should be same as above -->

            </div>


            <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-neutral-700 px-5 py-3">

                <button
                    @click="openLedger=false"
                    class="border border-blue-500 text-blue-500 px-4 py-1 rounded text-sm hover:bg-gray-50 dark:hover:bg-neutral-700">

                    Cancel

                </button>


                <button class="bg-blue-600 text-white px-4 py-1 rounded text-sm hover:bg-blue-700">
                    Add
                </button>

            </div>

        </div>
    </div>


    <script>
        $('#backBtn').click(function() {
            window.history.back()
        })
    </script>

    @endsection