<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @role('super-admin')
                        Welcome super-admin!
                    @endrole
                    <br>
                    @if(auth()->user()->can('edit'))
                        I can Edit!
                    @endif
                    <br>
                    @can('delete')
                        I can Delete!
                    @endcan
                    <br>
                    @can('view')
                        I can View!
                    @endcan
                    <br>
                    @can('customer view')
                        I am a customer
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
