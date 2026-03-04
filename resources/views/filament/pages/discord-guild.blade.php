<x-filament-panels::page>
    @if (empty($this->guildMembers))
        <x-filament::section>
            <div class="flex flex-col items-center justify-center py-12 gap-3 text-center">
                <x-filament::icon
                    icon="heroicon-o-server-stack"
                    class="h-12 w-12 text-gray-400"
                />
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">No guild data loaded</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Click <strong>Update from Discord</strong> to fetch the current member list from your Discord server.
                </p>
            </div>
        </x-filament::section>
    @else
        {{-- Role legend --}}
        @if (! empty($this->guildRoles))
            <x-filament::section :collapsible="true" :collapsed="true" heading="Guild Roles ({{ count($this->guildRoles) }})">
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->guildRoles as $role)
                        @php
                            $hex = $role['color'] ? '#' . str_pad(dechex($role['color']), 6, '0', STR_PAD_LEFT) : null;
                        @endphp
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                            @if ($hex)
                                style="background-color: {{ $hex }}1a; color: {{ $hex }}; ring-color: {{ $hex }}4d;"
                            @else
                                style="background-color: rgb(243 244 246); color: rgb(107 114 128);"
                            @endif
                        >
                            {{ $role['name'] }}
                        </span>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Members table --}}
        <x-filament::section>
            <x-slot name="heading">
                Members
                <span class="ml-2 text-sm font-normal text-gray-500">({{ count($this->guildMembers) }})</span>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Avatar</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Username</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Server Nickname</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Roles</th>
                            <th class="px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($this->guildMembers as $member)
                            @php
                                $isImported = in_array($member['discord_id'], $this->importedIds, true);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $isImported ? 'opacity-60' : '' }}">
                                <td class="px-4 py-3">
                                    @if ($member['avatar_url'])
                                        <img
                                            src="{{ $member['avatar_url'] }}"
                                            alt="{{ $member['username'] }}"
                                            class="h-9 w-9 rounded-full object-cover"
                                        />
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-xs font-bold text-gray-500 dark:text-gray-300">
                                            {{ strtoupper(substr($member['username'], 0, 2)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $member['username'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $member['nickname'] ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($member['role_ids'] as $roleId)
                                            @if (isset($this->guildRoles[$roleId]))
                                                @php
                                                    $role = $this->guildRoles[$roleId];
                                                    $hex = $role['color'] ? '#' . str_pad(dechex($role['color']), 6, '0', STR_PAD_LEFT) : null;
                                                @endphp
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                                    @if ($hex)
                                                        style="background-color: {{ $hex }}1a; color: {{ $hex }};"
                                                    @else
                                                        style="background-color: rgb(243 244 246); color: rgb(107 114 128);"
                                                    @endif
                                                >
                                                    {{ $role['name'] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($isImported)
                                        <span class="inline-flex items-center gap-1 text-xs text-success-600 dark:text-success-400 font-medium">
                                            <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4" />
                                            Imported
                                        </span>
                                    @else
                                        <x-filament::button
                                            size="sm"
                                            color="primary"
                                            wire:click="importMember('{{ $member['discord_id'] }}')"
                                        >
                                            Import
                                        </x-filament::button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
