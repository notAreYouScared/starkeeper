<?php

namespace App\Filament\Pages;

use App\Models\Member;
use App\Models\Unit;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class OrgHierarchy extends Page
{
    protected string $view = 'filament.pages.org-hierarchy';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function getNavigationLabel(): string
    {
        return 'Org Hierarchy';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Organisation';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public function getTitle(): string
    {
        return 'Organisation Hierarchy';
    }

    public function getLeaders(): Collection
    {
        return Member::where('org_role', 'leadership')
            ->orderBy('name')
            ->get();
    }

    public function getUnits(): Collection
    {
        return Unit::with([
            'teams.teamMembers.member',
        ])
            ->orderBy('name')
            ->get();
    }
}
