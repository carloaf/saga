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
                        <label for="rank_id" class="block text-sm font-medium text-gray-700">Posto/Graduação *</label>
                        <select id="rank_id" name="rank_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecione...</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->id }}" {{ old('rank_id') == $rank->id ? 'selected' : '' }}>
                                    {{ $rank->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('rank_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Organization -->
                    <div>
                        <label for="organization_id" class="block text-sm font-medium text-gray-700">Organização Militar *</label>
                        <select id="organization_id" name="organization_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecione...</option>
                            @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->name }}
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
                                <input type="radio" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }}
                                       class="text-green-600 focus:ring-green-500">
                                <span class="ml-2">Masculino</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}
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
</x-app-layout>
