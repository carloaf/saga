<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Completar Cadastro
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('register.complete.post') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Google User Info Display -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Informações do Google</h3>
                        <div class="flex items-center space-x-4">
                            @if($googleUser['avatar'])
                            <img src="{{ $googleUser['avatar'] }}" alt="Avatar" class="w-16 h-16 rounded-full">
                            @endif
                            <div>
                                <p class="font-medium">{{ $googleUser['name'] }}</p>
                                <p class="text-gray-600">{{ $googleUser['email'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- War Name -->
                    <div>
                        <label for="war_name" class="block text-sm font-medium text-gray-700">Nome de Guerra *</label>
                        <input type="text" id="war_name" name="war_name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               value="{{ old('war_name') }}">
                        @error('war_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rank -->
                    <div>
                        <select id="rank_id" name="rank_id" required class="select-enhanced">
                            <option value="" disabled selected>Selecione seu Posto/Graduação</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->id }}" {{ old('rank_id') == $rank->id ? 'selected' : '' }}>
                                    {{ $rank->abbreviation }} - {{ $rank->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('rank_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Armed Force -->
                    <div>
                        <label for="armed_force" class="block text-sm font-medium text-gray-700">Força Armada *</label>
                        <select id="armed_force" name="armed_force" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                onchange="toggleOrganizationField()">
                            <option value="" disabled selected>Selecione sua Força Armada</option>
                            <option value="EB" {{ old('armed_force') == 'EB' ? 'selected' : '' }}>Exército Brasileiro (EB)</option>
                            <option value="MB" {{ old('armed_force') == 'MB' ? 'selected' : '' }}>Marinha do Brasil (MB)</option>
                            <option value="FAB" {{ old('armed_force') == 'FAB' ? 'selected' : '' }}>Força Aérea Brasileira (FAB)</option>
                        </select>
                        @error('armed_force')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Organization (conditional) -->
                    <div id="organization-field" style="display: none;">
                        <label for="organization_id" class="block text-sm font-medium text-gray-700">Organização Militar *</label>
                        <select id="organization_id" name="organization_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="" disabled selected>Selecione sua Organização Militar</option>
                            @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->abbreviation }} - {{ $organization->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sexo *</label>
                        <div class="mt-2 space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="M" {{ old('gender') === 'M' ? 'checked' : '' }}
                                       class="text-green-600 focus:ring-green-500">
                                <span class="ml-2">Masculino</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="F" {{ old('gender') === 'F' ? 'checked' : '' }}
                                       class="text-green-600 focus:ring-green-500">
                                <span class="ml-2">Feminino</span>
                            </label>
                        </div>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ready at OM Date -->
                    <div>
                        <label for="ready_at_om_date" class="block text-sm font-medium text-gray-700">Data de Pronto na OM *</label>
                        <input type="date" id="ready_at_om_date" name="ready_at_om_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               value="{{ old('ready_at_om_date') }}">
                        @error('ready_at_om_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Completar Cadastro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
function toggleOrganizationField() {
    const armedForceSelect = document.getElementById('armed_force');
    const organizationField = document.getElementById('organization-field');
    const organizationSelect = document.getElementById('organization_id');
    
    if (armedForceSelect.value === 'EB') {
        // Mostrar campo de organização para Exército Brasileiro
        organizationField.style.display = 'block';
        organizationSelect.required = true;
    } else {
        // Ocultar campo de organização para outras forças
        organizationField.style.display = 'none';
        organizationSelect.required = false;
        organizationSelect.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleOrganizationField();
});
</script>
</x-app-layout>
