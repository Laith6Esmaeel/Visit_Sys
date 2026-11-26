<x-filament-panels::page>
    {{-- 1. مربع البحث برقم البادج --}}
    <x-filament::section>
        <form wire:submit="searchVisit" class="flex flex-col md:flex-row items-end gap-4">
            <div class="w-full">
                <label class="block text-sm font-bold mb-2">
                    {{ __('Badge Number') }}
                </label>
                <input
                    type="text"
                    wire:model="badgeNumber"
                    placeholder="V-20261125-0001"
                    autofocus
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />
            </div>

            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass" size="lg">
                {{ __('Search') }}
            </x-filament::button>
        </form>
    </x-filament::section>

    {{-- 2. عرض تفاصيل الزيارة ونموذج تغيير الحالة --}}
    @if ($visit)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            {{-- بطاقة تفاصيل الزيارة --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Visit Details') }} ({{ $visit->badge_number }})
                </x-slot>

                <div class="space-y-3 text-base">
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="font-bold text-gray-500">{{ __('Visitor') }}:</span>
                        <span class="font-bold">{{ $visit->visitor?->full_name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="font-bold text-gray-500">{{ __('National ID') }}:</span>
                        <span>{{ $visit->visitor?->national_id ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="font-bold text-gray-500">{{ __('Directorate') }}:</span>
                        <span>{{ $visit->directorate?->name }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="font-bold text-gray-500">{{ __('Room') }}:</span>
                        <span>{{ $visit->room?->room_number }} ({{ $visit->room?->department?->name }})</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="font-bold text-gray-500">{{ __('Visit Reason') }}:</span>
                        <span>{{ $visit->visit_reason }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="font-bold text-gray-500">{{ __('Check-in Time') }}:</span>
                        <span dir="ltr">{{ $visit->check_in?->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="font-bold text-gray-500">{{ __('Transaction Status') }}:</span>
                        <x-filament::badge :color="$visit->current_status->getColor()">
                            {{ $visit->current_status->getLabel() }}
                        </x-filament::badge>
                    </div>
                </div>
            </x-filament::section>

            {{-- بطاقة تغيير حالة المعاملة --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Update Transaction Status') }}
                </x-slot>

                <form wire:submit="updateStatus" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">
                            {{ __('New Status') }}
                        </label>
                        <select
                            wire:model="newStatus"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            @foreach ($this->statuses as $status)
                                <option value="{{ $status->value }}">
                                    {{ $status->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">
                            {{ __('Notes') }}
                        </label>
                        <textarea
                            wire:model="statusNotes"
                            rows="3"
                            placeholder="{{ __('Add notes about this status change (optional)') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        ></textarea>
                    </div>

                    <x-filament::button type="submit" color="success" icon="heroicon-o-check-circle" class="w-full">
                        {{ __('Save Status Update') }}
                    </x-filament::button>
                </form>
            </x-filament::section>
        </div>

        {{-- 3. سجل التغييرات السابقة لهذه المعاملة --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                {{ __('Transaction Status History') }}
            </x-slot>

            @if ($visit->statusHistories->isEmpty())
                <p class="text-gray-500 text-sm">{{ __('No status updates recorded yet.') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-start">
                        <thead class="border-b dark:border-gray-700">
                            <tr>
                                <th class="py-2 px-3 text-start">{{ __('Status') }}</th>
                                <th class="py-2 px-3 text-start">{{ __('Updated By (Employee)') }}</th>
                                <th class="py-2 px-3 text-start">{{ __('Notes') }}</th>
                                <th class="py-2 px-3 text-start">{{ __('Changed At') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($visit->statusHistories->sortByDesc('changed_at') as $history)
                                <tr>
                                    <td class="py-2 px-3">
                                        <x-filament::badge :color="$history->status->getColor()">
                                            {{ $history->status->getLabel() }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="py-2 px-3 font-bold">{{ $history->employee?->name ?? '—' }}</td>
                                    <td class="py-2 px-3">{{ $history->notes ?? '—' }}</td>
                                    <td class="py-2 px-3" dir="ltr">{{ $history->changed_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>