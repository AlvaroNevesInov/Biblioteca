<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar utilizador de teste (só se não existir)
        User::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'name' => 'Test User',
        'password' => Hash::make('password123'),
    ]
);

        // =========================================
        // EDITORAS
        // =========================================
        $editoras = [
            ['nome' => 'Porto Editora'],
            ['nome' => 'Leya'],
            ['nome' => 'Penguin Random House'],
            ['nome' => 'HarperCollins'],
            ['nome' => 'Companhia das Letras'],
            ['nome' => 'Presença'],
            ['nome' => 'Alfaguara'],
            ['nome' => 'Dom Quixote'],
        ];

        foreach ($editoras as $editora) {
            Editora::create($editora);
        }

        // =========================================
        // AUTORES
        // =========================================
        $autores = [
            ['nome' => 'José Saramago', 'foto' => null],
            ['nome' => 'Fernando Pessoa', 'foto' => null],
            ['nome' => 'Eça de Queirós', 'foto' => null],
            ['nome' => 'Machado de Assis', 'foto' => null],
            ['nome' => 'Gabriel García Márquez', 'foto' => null],
            ['nome' => 'George Orwell', 'foto' => null],
            ['nome' => 'J.K. Rowling', 'foto' => null],
            ['nome' => 'Agatha Christie', 'foto' => null],
            ['nome' => 'Paulo Coelho', 'foto' => null],
            ['nome' => 'Isabel Allende', 'foto' => null],
        ];

        foreach ($autores as $autor) {
            Autor::create($autor);
        }

        // =========================================
        // LIVROS
        // =========================================
        $livros = [
            [
                'isbn' => '978-9722034578',
                'nome' => 'Ensaio sobre a Cegueira',
                'editora_id' => 1,
                'bibliografia' => 'Um dos romances mais conhecidos de José Saramago, explorando a perda de visão como metáfora da sociedade moderna.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81kHhI1TjHL.jpg',
                'preco' => 15.99,
            ],
            [
                'isbn' => '978-9722019835',
                'nome' => 'Memorial do Convento',
                'editora_id' => 1,
                'bibliografia' => 'Uma das obras-primas de José Saramago, misturando ficção histórica e realismo mágico no Portugal do século XVIII.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81hWwP8Uv-L.jpg',
                'preco' => 14.50,
            ],
            [
                'isbn' => '978-9722020633',
                'nome' => 'Livro do Desassossego',
                'editora_id' => 6,
                'bibliografia' => 'Obra póstuma de Fernando Pessoa, repleta de reflexões filosóficas e existenciais.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/71Rk3fUvGmL.jpg',
                'preco' => 13.99,
            ],
            [
                'isbn' => '978-9722019422',
                'nome' => 'Mensagem',
                'editora_id' => 6,
                'bibliografia' => 'Único livro em português publicado em vida por Fernando Pessoa, reunindo poemas de exaltação nacional.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/71K9CbwXmpL.jpg',
                'preco' => 9.99,
            ],
            [
                'isbn' => '978-9722034219',
                'nome' => 'Os Maias',
                'editora_id' => 8,
                'bibliografia' => 'Romance clássico de Eça de Queirós que retrata a decadência da burguesia lisboeta.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81vN8vU+LML.jpg',
                'preco' => 17.50,
            ],
            [
                'isbn' => '978-8535911664',
                'nome' => 'Dom Casmurro',
                'editora_id' => 5,
                'bibliografia' => 'Um dos maiores romances de Machado de Assis, abordando ciúme, memória e ambiguidade narrativa.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81mK1Q6xFQL.jpg',
                'preco' => 12.90,
            ],
            [
                'isbn' => '978-9722344791',
                'nome' => '1984',
                'editora_id' => 3,
                'bibliografia' => 'Clássico distópico de George Orwell sobre vigilância, totalitarismo e manipulação da verdade.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81vpsIs58WL.jpg',
                'preco' => 11.99,
            ],
            [
                'isbn' => '978-9722344814',
                'nome' => 'Harry Potter e a Pedra Filosofal',
                'editora_id' => 6,
                'bibliografia' => 'Primeiro livro da saga Harry Potter, de J.K. Rowling, que apresenta o jovem feiticeiro e o mundo mágico.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81YOuOGFCJL.jpg',
                'preco' => 16.75,
            ],
            [
                'isbn' => '978-9722344838',
                'nome' => 'Assassinato no Expresso do Oriente',
                'editora_id' => 4,
                'bibliografia' => 'Um dos mistérios mais famosos de Agatha Christie, protagonizado por Hercule Poirot.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81gTwYAhU7L.jpg',
                'preco' => 10.99,
            ],
            [
                'isbn' => '978-9722344852',
                'nome' => 'A Casa dos Espíritos',
                'editora_id' => 7,
                'bibliografia' => 'Saga familiar e política que lançou Isabel Allende ao reconhecimento internacional.',
                'imagem_capa' => 'https://m.media-amazon.com/images/I/81bVZ1Z+KpL.jpg',
                'preco' => 14.80,
            ],
        ];

        foreach ($livros as $livro) {
            Livro::create($livro);
        }

        // ======================================================
        // ASSOCIAÇÕES LIVRO ↔ AUTORES (TABELA PIVOT)
        // ======================================================
        $associacoes = [
            1 => [1], // Ensaio sobre a Cegueira → José Saramago
            2 => [1], // Memorial do Convento → José Saramago
            3 => [2], // Livro do Desassossego → Fernando Pessoa
            4 => [2], // Mensagem → Fernando Pessoa
            5 => [3], // Os Maias → Eça de Queirós
            6 => [4], // Dom Casmurro → Machado de Assis
            7 => [6], // 1984 → George Orwell
            8 => [7], // Harry Potter → J.K. Rowling
            9 => [8], // Assassinato → Agatha Christie
            10 => [10], // Casa dos Espíritos → Isabel Allende
        ];

        foreach ($associacoes as $livroId => $autorIds) {
            $livro = Livro::find($livroId);
            if ($livro) {
                $livro->autores()->attach($autorIds);
            }
        }

        $this->command->info('✅ Base de dados populada com sucesso!');
        $this->command->info('📚 8 Editoras | 10 Autores | 10 Livros');
    }
}
