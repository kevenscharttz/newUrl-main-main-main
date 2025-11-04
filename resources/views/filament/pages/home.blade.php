<x-filament-panels::page>
    <div class="p-6 space-y-8 rounded-xl shadow-sm max-w-7xl mx-auto dark:bg-gray-950">
        <!-- Cards de métricas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-8 bg-gray-50 border rounded-xl dark:bg-gray-900 dark:border-gray-800 flex flex-col items-center">
                <h3 class="text-gray-700 font-semibold mb-2 dark:text-gray-200 text-lg">Dashboards Ativos</h3>
                <p class="text-4xl font-extrabold text-gray-900 dark:text-blue-300">{{ $dashboardsCount }}</p>
                <!-- Porcentagem removida -->
            </div>
            <div class="p-8 bg-gray-50 border rounded-xl dark:bg-gray-900 dark:border-gray-800 flex flex-col items-center">
                <h3 class="text-gray-700 font-semibold mb-2 dark:text-gray-200 text-lg">Usuários Ativos</h3>
                <p class="text-4xl font-extrabold text-gray-900 dark:text-blue-300">{{ $usersCount }}</p>
                <!-- Porcentagem removida -->
            </div>
            <div class="p-8 bg-gray-50 border rounded-xl dark:bg-gray-900 dark:border-gray-800 flex flex-col items-center">
                <h3 class="text-gray-700 font-semibold mb-2 dark:text-gray-200 text-lg">Organizações</h3>
                <p class="text-4xl font-extrabold text-gray-900 dark:text-blue-300">{{ $organizationsCount }}</p>
                <!-- Porcentagem removida -->
            </div>
        </div>
        <!-- Dashboards Recentes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="p-5 bg-gray-50 border rounded-lg dark:bg-gray-900 dark:border-gray-800 w-full mt-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-blue-200">Dashboards Recentes</h2>
                    <a href="{{ \App\Filament\Resources\Dashboards\DashboardResource::getUrl('index') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">Ver Todos</a>
                </div>
                <ul class="space-y-3 w-full">
                    @foreach($recentDashboards as $d)
                        <li class="flex items-center justify-between p-3 bg-white rounded-md shadow-sm dark:bg-gray-800">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-blue-100">{{ $d->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $d->organization?->name }}</p>
                            </div>
                            <span class="text-sm bg-blue-100 text-blue-700 px-2 py-1 rounded-full dark:bg-blue-950 dark:text-blue-300">{{ $d->views ?? '0' }} views</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- Ações Rápidas -->
        <div class="p-5 bg-gray-50 border rounded-lg dark:bg-gray-900 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-blue-200 mb-3">Ações Rápidas</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Comece a trabalhar com dados rapidamente</p>
            <div class="flex flex-wrap gap-4 mt-4">
                <a href="{{ \App\Filament\Resources\Dashboards\DashboardResource::getUrl('index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium shadow hover:bg-blue-700 transition dark:bg-blue-700 dark:hover:bg-blue-800">Ver Dashboards</a>
                <a href="{{ \App\Filament\Resources\Reports\ReportResource::getUrl('index') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium shadow hover:bg-green-700 transition dark:bg-green-700 dark:hover:bg-green-800">Ver Relatórios</a>
                <a href="{{ \App\Filament\Resources\Organizations\OrganizationResource::getUrl('index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium shadow hover:bg-purple-700 transition dark:bg-purple-700 dark:hover:bg-purple-800">Ver Organizações</a>
                <a href="{{ \App\Filament\Resources\Users\UserResource::getUrl('index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg font-medium shadow hover:bg-gray-700 transition dark:bg-gray-700 dark:hover:bg-gray-800">Ver Usuários</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
