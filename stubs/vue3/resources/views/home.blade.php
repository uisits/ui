<x-app-layout>

    <!-- Set CSS Styles for current page. P.S. Can be Omitted -->
    <x-slot name="styles">
        <style></style>
    </x-slot>

    <!-- Set Title. P.S. Can be Omitted -->
    <x-slot name="title">ITS-Home</x-slot>

    <!-- Header tag. P.S. Can be Omitted -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <!-- Page Content goes here! -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                {{ \Illuminate\Foundation\Inspiring::quote() }}
            </div>
        </div>
    </div>

    <!-- Set JavaScript for current page. P.S. Can be Omitted -->
    <x-slot name="scripts">
        <script></script>
    </x-slot>

</x-app-layout>
