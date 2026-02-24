<?php

namespace App\Filament\Widgets;

use App\Models\CalendarEvent;
use App\Models\Unit;
use Carbon\WeekDay;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent as CalendarEventObject;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

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
        $id = Unit::where('name', $this->getUnitName())->value('id');

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

    protected function calendarEventSchema(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Event Name')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Description')
                ->maxLength(65535),
            DateTimePicker::make('start')
                ->label('Start')
                ->required(),
            DateTimePicker::make('end')
                ->label('End')
                ->required(),
            Toggle::make('all_day')
                ->label('All Day')
                ->default(false),
        ]);
    }
}
