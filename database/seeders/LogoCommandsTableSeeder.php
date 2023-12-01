<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LogoCommand;

class LogoCommandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table("logo_commands")->insert(['commands' => 'AV',]);
        DB::table("logo_commands")->insert(['commands' => 'RE',]);
        DB::table("logo_commands")->insert(['commands' => 'TD',]);
        DB::table("logo_commands")->insert(['commands' => 'TG',]);
        DB::table("logo_commands")->insert(['commands' => 'LC',]);
        DB::table("logo_commands")->insert(['commands' => 'BC',]);
        DB::table("logo_commands")->insert(['commands' => 'CT',]);
        DB::table("logo_commands")->insert(['commands' => 'MC',]);
        DB::table("logo_commands")->insert(['commands' => 'VE',]);
        DB::table("logo_commands")->insert(['commands' => 'NETTOIE',]);
        DB::table("logo_commands")->insert(['commands' => 'ORIGINE',]);
        DB::table("logo_commands")->insert(['commands' => 'VT',]);
        DB::table("logo_commands")->insert(['commands' => 'FCC',]);
        DB::table("logo_commands")->insert(['commands' => 'FCB',]);
        DB::table("logo_commands")->insert(['commands' => 'FCAP',]);
        DB::table("logo_commands")->insert(['commands' => 'CAP',]);
        DB::table("logo_commands")->insert(['commands' => 'FPOS',]);
        DB::table("logo_commands")->insert(['commands' => 'POS',]);
       
    }
}
