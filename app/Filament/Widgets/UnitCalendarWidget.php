<?php

namespace App\Filament\Widgets;

use App\Models\CalendarEvent;
use Carbon\WeekDay;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent as CalendarEventObject;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

abstract class UnitCalendarWidget extends CalendarWidget
{
    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    protected WeekDay $firstDay = WeekDay::Sunday;

    public function getHeading(): string
    {
        return $this->getUnitName() . ' Calendar';
    }

    abstract protected function getUnitName(): string;

    protected function getUnitId(): int
    {
        $id = \App\Models\Unit::where('name', $this->getUnitName())->value('id');

        if ($id === null) {
            throw new \RuntimeException("Unit '{$this->getUnitName()}' not found. Ensure the database has been seeded.");
        }

        return $id;
    }

    protected function getEvents(FetchInfo $info): Collection|array
    {
        return CalendarEvent::query()
            ->where('unit_id', $this->getUnitId())
            ->whereBetween('start', [$info->start, $info->end])
            ->get()
            ->map(fn (CalendarEvent $event) => CalendarEventObject::make($event)
                ->title($event->title)
                ->start($event->start)
                ->end($event->end)
                ->allDay($event->all_day)
            );
    }

    public function getHeaderActions(): array
    {
        if (! auth()->user()?->is_admin) {
            return [];
        }

        return [
            $this->createAction(CalendarEvent::class)
                ->mutateFormDataUsing(fn (array $data) => array_merge($data, ['unit_id' => $this->getUnitId()])),
        ];
    }

    protected function calendarEventSchema(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('title')
                ->label('Event Title')
                ->required()
                ->maxLength(255),
            \Filament\Forms\Components\DateTimePicker::make('start')
                ->label('Start')
                ->required(),
            \Filament\Forms\Components\DateTimePicker::make('end')
                ->label('End')
                ->required(),
            \Filament\Forms\Components\Toggle::make('all_day')
                ->label('All Day')
                ->default(false),
        ]);
    }
}
