<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Help') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-5 text-center bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="text-4xl font-bold ">Let us help you!</div>
                <p class="text-gray-600 font-normal">
                    Please contact <a class="inline-block rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-600 px-1 py-2" href="mailto:techsupport@uis.edu?subject={{ env('APP_NAME') }} Help">techsupport@uis.edu</a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
