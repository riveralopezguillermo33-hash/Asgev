<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

it('rejects a score outside the 0 to 20 range in the database', function () {
    expect(fn () => DB::table('notas')->insert([
        'colegio_id' => 999,
        'matricula_id' => 999,
        'competencia_id' => 999,
        'periodo_id' => 999,
        'nota' => 20.01,
        'registrado_por' => 999,
    ]))->toThrow(QueryException::class);
});
