<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get states to reference
        $sp = State::where('code', 'SP')->first();
        $rj = State::where('code', 'RJ')->first();
        $mg = State::where('code', 'MG')->first();
        $rs = State::where('code', 'RS')->first();
        $pr = State::where('code', 'PR')->first();
        $ba = State::where('code', 'BA')->first();
        $go = State::where('code', 'GO')->first();
        $sc = State::where('code', 'SC')->first();
        
        $cities = [
            // São Paulo
            ['name' => 'São Paulo', 'state_id' => $sp->id],
            ['name' => 'Campinas', 'state_id' => $sp->id],
            ['name' => 'Santos', 'state_id' => $sp->id],
            ['name' => 'Ribeirão Preto', 'state_id' => $sp->id],
            ['name' => 'São José dos Campos', 'state_id' => $sp->id],
            ['name' => 'Sorocaba', 'state_id' => $sp->id],
            ['name' => 'Osasco', 'state_id' => $sp->id],
            ['name' => 'Santo André', 'state_id' => $sp->id],
            ['name' => 'São Bernardo do Campo', 'state_id' => $sp->id],
            ['name' => 'Guarulhos', 'state_id' => $sp->id],
            
            // Rio de Janeiro
            ['name' => 'Rio de Janeiro', 'state_id' => $rj->id],
            ['name' => 'Niterói', 'state_id' => $rj->id],
            ['name' => 'Petrópolis', 'state_id' => $rj->id],
            ['name' => 'Campos dos Goytacazes', 'state_id' => $rj->id],
            ['name' => 'Nova Iguaçu', 'state_id' => $rj->id],
            ['name' => 'Duque de Caxias', 'state_id' => $rj->id],
            ['name' => 'São Gonçalo', 'state_id' => $rj->id],
            ['name' => 'Volta Redonda', 'state_id' => $rj->id],
            
            // Minas Gerais
            ['name' => 'Belo Horizonte', 'state_id' => $mg->id],
            ['name' => 'Uberlândia', 'state_id' => $mg->id],
            ['name' => 'Contagem', 'state_id' => $mg->id],
            ['name' => 'Juiz de Fora', 'state_id' => $mg->id],
            ['name' => 'Betim', 'state_id' => $mg->id],
            ['name' => 'Montes Claros', 'state_id' => $mg->id],
            ['name' => 'Ribeirão das Neves', 'state_id' => $mg->id],
            ['name' => 'Uberaba', 'state_id' => $mg->id],
            
            // Rio Grande do Sul
            ['name' => 'Porto Alegre', 'state_id' => $rs->id],
            ['name' => 'Caxias do Sul', 'state_id' => $rs->id],
            ['name' => 'Pelotas', 'state_id' => $rs->id],
            ['name' => 'Canoas', 'state_id' => $rs->id],
            ['name' => 'Santa Maria', 'state_id' => $rs->id],
            ['name' => 'Gravataí', 'state_id' => $rs->id],
            ['name' => 'Viamão', 'state_id' => $rs->id],
            ['name' => 'Novo Hamburgo', 'state_id' => $rs->id],
            
            // Paraná
            ['name' => 'Curitiba', 'state_id' => $pr->id],
            ['name' => 'Londrina', 'state_id' => $pr->id],
            ['name' => 'Maringá', 'state_id' => $pr->id],
            ['name' => 'Ponta Grossa', 'state_id' => $pr->id],
            ['name' => 'Cascavel', 'state_id' => $pr->id],
            ['name' => 'São José dos Pinhais', 'state_id' => $pr->id],
            ['name' => 'Foz do Iguaçu', 'state_id' => $pr->id],
            ['name' => 'Colombo', 'state_id' => $pr->id],
            
            // Bahia
            ['name' => 'Salvador', 'state_id' => $ba->id],
            ['name' => 'Feira de Santana', 'state_id' => $ba->id],
            ['name' => 'Vitória da Conquista', 'state_id' => $ba->id],
            ['name' => 'Camaçari', 'state_id' => $ba->id],
            ['name' => 'Itabuna', 'state_id' => $ba->id],
            ['name' => 'Juazeiro', 'state_id' => $ba->id],
            ['name' => 'Lauro de Freitas', 'state_id' => $ba->id],
            ['name' => 'Ilhéus', 'state_id' => $ba->id],
            
            // Goiás
            ['name' => 'Goiânia', 'state_id' => $go->id],
            ['name' => 'Aparecida de Goiânia', 'state_id' => $go->id],
            ['name' => 'Anápolis', 'state_id' => $go->id],
            ['name' => 'Rio Verde', 'state_id' => $go->id],
            ['name' => 'Luziânia', 'state_id' => $go->id],
            ['name' => 'Águas Lindas de Goiás', 'state_id' => $go->id],
            ['name' => 'Valparaíso de Goiás', 'state_id' => $go->id],
            ['name' => 'Trindade', 'state_id' => $go->id],
            
            // Santa Catarina
            ['name' => 'Florianópolis', 'state_id' => $sc->id],
            ['name' => 'Joinville', 'state_id' => $sc->id],
            ['name' => 'Blumenau', 'state_id' => $sc->id],
            ['name' => 'São José', 'state_id' => $sc->id],
            ['name' => 'Criciúma', 'state_id' => $sc->id],
            ['name' => 'Chapecó', 'state_id' => $sc->id],
            ['name' => 'Itajaí', 'state_id' => $sc->id],
            ['name' => 'Jaraguá do Sul', 'state_id' => $sc->id],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
