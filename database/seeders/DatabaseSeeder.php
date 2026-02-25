<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use App\Models\OrgRole;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $orgRoles = [
            ['name' => 'leadership', 'label' => 'Leadership', 'sort_order' => 1],
            ['name' => 'director',   'label' => 'Director',   'sort_order' => 2],
            ['name' => 'mod',        'label' => 'Mod',        'sort_order' => 3],
            ['name' => 'member',     'label' => 'Member',     'sort_order' => 4],
        ];

        foreach ($orgRoles as $role) {
            OrgRole::firstOrCreate(['name' => $role['name']], $role);
        }

        $units = [
            [
                'name' => 'Security',
                'description' => 'Handles fleet protection, combat operations, and base defence across all theatres.',
            ],
            [
                'name' => 'Industry',
                'description' => 'Manages mining, salvage, cargo hauling, and resource logistics for the organisation.',
            ],
            [
                'name' => 'Racing',
                'description' => 'Competes in Murray Cup and other racing circuits, driving development of high-speed craft.',
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }

        $contentPages = [
            [
                'slug'    => 'home',
                'title'   => 'Home',
                'content' => "Welcome to **Starkeeper Industries**\n\nWe mine it. We move it. We (probably) blow it.",
            ],
            [
                'slug'    => 'history',
                'title'   => 'History',
                'content' => "Looking for an org that's chill, semi-functional, and slightly obsessed with loot? You found us.\n\nStarkeeper Industries is a US-based, laid-back org with no activity requirements, weekly events, and a proud tradition of barely holding it together and still winning. We're a bunch of casual misfits who like to run missions, talk trash, and make a mess of the 'verse—with just enough structure to keep things from catching fire. (Well, most of the time.)\n\nStarkeeper Industries is built around good people, chill vibes, and a healthy dose of space chaos. We welcome everyone from veteran players to brand-new pilots and encourage members to jump in, squad up, and have fun. Whether you're moving cargo, mining ore, running escort, or just causing \"accidental\" explosions, there's a place for you here.\n\nThere are no activity requirements — life comes first. We're a dysfunctional family in the best way: supportive, sarcastic, and always down for a good time (or at least a decent crash landing). If you're looking for an org that's active without being sweaty, organized without being strict, and full of people who laugh when things go wrong… you're in the right place.\n\nBecause space is dangerous, weird, and way more fun with people who don't mind laughing through the chaos.",
            ],
            [
                'slug'    => 'manifesto',
                'title'   => 'Manifesto',
                'content' => "## Our Mission\n\nExplore boldly, operate together, and enjoy the chaos.\n\nWe're here to build a strong, supportive, and hilarious community of players who thrive in all corners of the 'verse — from mining rocks and hauling cargo to jumping into firefights and forgetting to bring medpens.\n\nWe believe in teamwork without pressure, structure without rigidity, and fun above all else. Through our three divisions — Logistics, Industry, and Security — we aim to create opportunities for every member to find their place, chase their goals, and make some questionable decisions with good company.\n\n**Because in the end, space is cold — but it doesn't have to be lonely.**",
            ],
            [
                'slug'    => 'charter',
                'title'   => 'Charter',
                'content' => "## I. Purpose\n\nStarkeeper Industries exists to bring together explorers, fighters, traders, and all-around weirdos from across the 'verse in one laid-back, often chaotic but oddly effective organization. Whether you're hauling cargo, mining rocks, or storming bunkers, there's a place for you here. Our goal? Have fun, blow stuff up (on purpose or not), and look good doing it.\n\n## II. Structure\n\n- **Industry** – Mining, refining, repairing, salvaging—basically the folks who make the money and fix the stuff we break.\n- **Logistics** – Hauling gear, moving assets, organizing supply lines, and always forgetting where they parked.\n- **Security** – If it shoots, they bring it. From bunker clearing to escort missions, these are the trigger-happy pros (or close enough).\n\nEach branch is autonomous but works together like a well-lubed multicrew ship… on a good day.\n\n## III. Membership\n\n- Say hi.\n- Don't be a jerk.\n- Pick a Branch (or don't — wander the void!).\n- Participate when you want; we don't do mandatory attendance.\n\n## IV. Culture\n\n- We are casual-first, fun-second, professional-never (unless we really have to).\n- Weekly events usually start at 7 or 9 PM EST — pop in or out as you like.\n- We're a dysfunctional family in the best way: chaotic, loyal, and always ready to revive you when you faceplant in a bunker.\n\n## V. Rules\n\n- We joke a lot, but we take respect and inclusion seriously.\n- No harassment, hate speech, or griefing. We don't tolerate that.\n- Follow leadership guidance, especially in missions.\n- Most importantly: bring snacks. Or ammo. Preferably both.\n\n## VI. Leadership\n\nLeadership exists to guide the chaos, not crush it. Branch leads and team leads help coordinate events, answer questions, and keep things moving. Think of them less like bosses and more like your slightly responsible space uncles and aunts.",
            ],
        ];

        foreach ($contentPages as $page) {
            ContentPage::firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
