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
        // Criar 1 administrador
        User::factory()->admin()->verified()->create();

        // Criar 15 utilizadores portugueses
        User::factory()
            ->count(15)
            ->portuguese()
            ->verified()
            ->create();


        // =========================================
        // EDITORAS
        // =========================================
        $editoras = [
            ['nome' => 'Porto Editora', 'logotipo' => 'https://cotecportugal.pt/wp-content/uploads/2020/01/Porto-Editora-1-1200x588.jpg'],
            ['nome' => 'Leya', 'logotipo' => 'https://www.leya.com/asset/img/LeYa-logo.png'],
            ['nome' => 'Penguin Random House', 'logotipo' => 'https://logowik.com/content/uploads/images/penguin-random-house-new-20242012.logowik.com.webp'],
            ['nome' => 'HarperCollins', 'logotipo' =>'https://s21618.pcdn.co/wp-content/uploads/2016/12/FireandWaterLogo-768x831.jpg'],
            ['nome' => 'Companhia das Letras', 'logotipo' => 'https://www.penguinrandomhousegrupoeditorial.com/pt/wp-content/uploads/2022/05/sello-companhia-das-letras-penguinrandomhousegrupoeditorial.png'],
            ['nome' => 'Presença', 'logotipo' => 'https://www.empregoestagios.com/wp-content/uploads/2023/03/Editorial-Presenca-310x165.png.webp'],
            ['nome' => 'Alfaguara', 'logotipo' => 'https://www.penguinrandomhousegrupoeditorial.com/pt/wp-content/uploads/2020/12/sellos_ALFAGUARA-parrilla.png'],
            ['nome' => 'Dom Quixote', 'logotipo' => 'https://www.leya.com/storage/images/1536ed77945743b1c13b38fe4652dece.png'],
        ];

        foreach ($editoras as $editora) {
            Editora::create($editora);
        }

        // =========================================
        // AUTORES
        // =========================================
        $autores = [
            ['nome' => 'José Saramago',               'foto' => 'https://upload.wikimedia.org/wikipedia/commons/8/82/Saramago%2C_Jos%C3%A9_%281922%29-2.jpg'],
            ['nome' => 'Fernando Pessoa',             'foto' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/216_2310-Fernando-Pessoa.jpg'],
            ['nome' => 'Eça de Queirós',              'foto' => 'https://upload.wikimedia.org/wikipedia/commons/d/d9/E%C3%A7a_de_Queir%C3%B3s_c._1882.jpg'],
            ['nome' => 'Machado de Assis',            'foto' => 'https://upload.wikimedia.org/wikipedia/commons/4/40/Machado_de_Assis_aos_57_anos.jpg'],
            ['nome' => 'Gabriel García Márquez',      'foto' => 'https://upload.wikimedia.org/wikipedia/commons/0/0f/Gabriel_Garcia_Marquez.jpg'],
            ['nome' => 'George Orwell',               'foto' => 'https://upload.wikimedia.org/wikipedia/commons/7/7e/George_Orwell_press_photo.jpg'],
            ['nome' => 'J.K. Rowling',                'foto' => 'https://upload.wikimedia.org/wikipedia/commons/5/5d/J._K._Rowling_2010.jpg'],
            ['nome' => 'Agatha Christie',             'foto' => 'https://upload.wikimedia.org/wikipedia/commons/c/cf/Agatha_Christie.png'],
            ['nome' => 'Paulo Coelho',                'foto' => 'https://upload.wikimedia.org/wikipedia/commons/a/a7/Paulo_Coelho_30102007.jpg'],
            ['nome' => 'Isabel Allende',              'foto' => 'https://upload.wikimedia.org/wikipedia/commons/d/db/Isabel_Allende_-_001.jpg'],
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
                'imagem_capa' => 'https://img.wook.pt/images/ensaio-sobre-a-cegueira-jose-saramago/MXwxNTgyNTQ4NnwyNjMwODgzNnwxNzEzOTY4NjM3MDAw/500x',
                'preco' => 15.99,
            ],
            [
                'isbn' => '978-9722019835',
                'nome' => 'Memorial do Convento',
                'editora_id' => 1,
                'bibliografia' => 'Uma das obras-primas de José Saramago, misturando ficção histórica e realismo mágico no Portugal do século XVIII.',
                'imagem_capa' => 'https://upload.wikimedia.org/wikipedia/pt/thumb/8/85/Memorial_do_convento_%2848%C2%AA_edi%C3%A7%C3%A3o%29.jpg/250px-Memorial_do_convento_%2848%C2%AA_edi%C3%A7%C3%A3o%29.jpg',
                'preco' => 14.50,
            ],
            [
                'isbn' => '978-9722020633',
                'nome' => 'Livro do Desassossego',
                'editora_id' => 6,
                'bibliografia' => 'Obra póstuma de Fernando Pessoa, repleta de reflexões filosóficas e existenciais.',
                'imagem_capa' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR7WH-zCwglDXLuEnp1z_IiCxKu7dHb7gA3vqzLuiu39j3pHnJn6xHwrMmqwHhWhKFT2pN3MmtUXFxr4Ldv8BVlgSVnD82Iq5a5dPrOVO6kqg&s=10',
                'preco' => 13.99,
            ],
            [
                'isbn' => '978-9722019422',
                'nome' => 'Mensagem',
                'editora_id' => 6,
                'bibliografia' => 'Único livro em português publicado em vida por Fernando Pessoa, reunindo poemas de exaltação nacional.',
                'imagem_capa' => 'https://pt.wikipedia.org/wiki/Mensagem_%28livro%29#/media/Ficheiro:1edicao_Mensagem_1934.jpg',
                'preco' => 9.99,
            ],
            [
                'isbn' => '978-9722034219',
                'nome' => 'Os Maias',
                'editora_id' => 8,
                'bibliografia' => 'Romance clássico de Eça de Queirós que retrata a decadência da burguesia lisboeta.',
                'imagem_capa' => 'https://img.bertrand.pt/images/os-maias-eca-de-queiroz/NDV8MTUzMjgzNDZ8MjczOTkwNTR8MTY5OTQzNjcxMDAwMHx3ZWJw/300x',
                'preco' => 17.50,
            ],
            [
                'isbn' => '978-8535911664',
                'nome' => 'Dom Casmurro',
                'editora_id' => 5,
                'bibliografia' => 'Um dos maiores romances de Machado de Assis, abordando ciúme, memória e ambiguidade narrativa.',
                'imagem_capa' => 'https://upload.wikimedia.org/wikipedia/commons/0/05/DomCasmurroMachadodeAssis.jpg',
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
                'imagem_capa' => 'https://img.wook.pt/images/a-casa-dos-espiritos-isabel-allende/MXwxNDU5MTQ2NHwyODYwMTcxOXwxNzUyNzcyOTg4MDAwfHdlYnA=/550x',
                'preco' => 14.80,
            ],
        ];

        foreach ($livros as $livro) {
            Livro::create($livro);
        }

        // ======================================================
        // ASSOCIAÇÕES LIVRO ↔ AUTORES
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
