<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('abbreviation', 20)->nullable()->after('name');
        });

        $knownAbbreviations = [
            'Biji Gebang' => 'GBG',
            'Biji Pala' => 'BPL',
            'Bunga Pala' => 'BPA',
            'Cengkeh' => 'CKG',
            'Damar Batu' => 'DBT',
            'Gagang Cengkeh' => 'GCK',
            'Kopra' => 'KPR',
            'Kunyit' => 'KYT',
            'Kutulak' => 'LAC',
            'Mente' => 'MNT',
        ];

        $usedAbbreviations = [];

        DB::table('products')
            ->orderBy('company_id')
            ->orderBy('id')
            ->get(['id', 'company_id', 'name'])
            ->each(function (object $product) use ($knownAbbreviations, &$usedAbbreviations): void {
                $companyAbbreviations = $usedAbbreviations[$product->company_id] ?? [];
                $baseAbbreviation = $knownAbbreviations[$product->name]
                    ?? $this->abbreviateProductName($product->name);
                $abbreviation = $baseAbbreviation;
                $suffix = 2;

                while (in_array($abbreviation, $companyAbbreviations, true)) {
                    $abbreviation = Str::limit($baseAbbreviation, 20 - Str::length((string) $suffix), '').$suffix;
                    $suffix++;
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['abbreviation' => $abbreviation]);

                $usedAbbreviations[$product->company_id][] = $abbreviation;
            });

        Schema::table('products', function (Blueprint $table) {
            $table->unique(['company_id', 'abbreviation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'abbreviation']);
            $table->dropColumn('abbreviation');
        });
    }

    private function abbreviateProductName(string $name): string
    {
        $normalizedName = (string) Str::of($name)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', ' ')
            ->squish();
        $words = Str::of($normalizedName)->explode(' ')->filter();

        if ($words->count() > 1) {
            return $words
                ->map(fn (string $word): string => Str::substr($word, 0, 1))
                ->implode('');
        }

        $withoutVowels = preg_replace('/[AEIOU]/', '', $normalizedName) ?: $normalizedName;

        return Str::limit($withoutVowels, 4, '');
    }
};
