<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LivroControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Admin pode visualizar listagem de livros
     */
    public function test_admin_pode_visualizar_listagem_livros(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('livros.index'));

        $response->assertStatus(200);
        $response->assertViewIs('livros.index');
    }

    /**
     * Teste: Cidadão pode visualizar listagem de livros
     */
    public function test_cidadao_pode_visualizar_listagem_livros(): void
    {
        $cidadao = User::factory()->cidadao()->create();

        $response = $this->actingAs($cidadao)->get(route('livros.index'));

        $response->assertStatus(200);
        $response->assertViewIs('livros.index');
    }

    /**
     * Teste: Pode visualizar detalhes de um livro
     */
    public function test_pode_visualizar_detalhes_livro(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Livro $livro */
        $livro = Livro::factory()->create();

        $response = $this->actingAs($user)->get(route('livros.show', $livro));

        $response->assertStatus(200);
        $response->assertViewIs('livros.show');
        $response->assertViewHas('livro');
    }

    /**
     * Teste: Admin pode acessar formulário de criação
     */
    public function test_admin_pode_acessar_formulario_criacao(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('livros.create'));

        $response->assertStatus(200);
        $response->assertViewIs('livros.create');
        $response->assertViewHas(['editoras', 'autores']);
    }

    /**
     * Teste: Admin pode criar livro
     */
    public function test_admin_pode_criar_livro(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $editora = Editora::factory()->create();
        $autor = Autor::factory()->create();

        $imagem = UploadedFile::fake()->image('capa.jpg');

        $response = $this->actingAs($admin)->post(route('livros.store'), [
            'isbn' => '978-3-16-148410-0',
            'nome' => 'O Teste dos Testes',
            'editora_id' => $editora->id,
            'bibliografia' => 'Um livro sobre testes',
            'imagem_capa' => $imagem,
            'preco' => 19.99,
            'autores' => [$autor->id],
        ]);

        $response->assertRedirect(route('livros.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('livros', [
            'isbn' => '978-3-16-148410-0',
            'nome' => 'O Teste dos Testes',
            'editora_id' => $editora->id,
        ]);

        // Verificar relação com autores
        $livro = Livro::where('isbn', '978-3-16-148410-0')->first();
        $this->assertTrue($livro->autores->contains($autor));
    }

    /**
     * Teste: Não pode criar livro sem campos obrigatórios
     */
    public function test_nao_pode_criar_livro_sem_campos_obrigatorios(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('livros.store'), [
            'isbn' => '',
            'nome' => '',
            'editora_id' => '',
        ]);

        $response->assertSessionHasErrors(['isbn', 'nome', 'editora_id', 'autores']);
    }

    /**
     * Teste: ISBN deve ser único
     */
    public function test_isbn_deve_ser_unico(): void
    {
        $admin = User::factory()->admin()->create();
        $editora = Editora::factory()->create();
        $autor = Autor::factory()->create();

        // Criar primeiro livro
        $livro = Livro::factory()->create(['isbn' => '978-1-234-56789-0']);

        // Tentar criar outro com mesmo ISBN
        $response = $this->actingAs($admin)->post(route('livros.store'), [
            'isbn' => '978-1-234-56789-0',
            'nome' => 'Outro Livro',
            'editora_id' => $editora->id,
            'autores' => [$autor->id],
        ]);

        $response->assertSessionHasErrors('isbn');
    }

    /**
     * Teste: Livro precisa ter pelo menos um autor
     */
    public function test_livro_precisa_ter_pelo_menos_um_autor(): void
    {
        $admin = User::factory()->admin()->create();
        $editora = Editora::factory()->create();

        $response = $this->actingAs($admin)->post(route('livros.store'), [
            'isbn' => '978-1-234-56789-1',
            'nome' => 'Livro Sem Autor',
            'editora_id' => $editora->id,
            'autores' => [],
        ]);

        $response->assertSessionHasErrors('autores');
    }

    /**
     * Teste: Admin pode acessar formulário de edição
     */
    public function test_admin_pode_acessar_formulario_edicao(): void
    {
        $admin = User::factory()->admin()->create();
        $livro = Livro::factory()->create();

        $response = $this->actingAs($admin)->get(route('livros.edit', $livro));

        $response->assertStatus(200);
        $response->assertViewIs('livros.edit');
        $response->assertViewHas(['livro', 'editoras', 'autores']);
    }

    /**
     * Teste: Admin pode atualizar livro
     */
    public function test_admin_pode_atualizar_livro(): void
    {
        $admin = User::factory()->admin()->create();
        $livro = Livro::factory()->create(['nome' => 'Nome Antigo']);
        $autor = Autor::factory()->create();

        $response = $this->actingAs($admin)->put(route('livros.update', $livro), [
            'isbn' => $livro->isbn,
            'nome' => 'Nome Atualizado',
            'editora_id' => $livro->editora_id,
            'bibliografia' => 'Bibliografia atualizada',
            'preco' => 29.99,
            'autores' => [$autor->id],
        ]);

        $response->assertRedirect(route('livros.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'nome' => 'Nome Atualizado',
        ]);
    }

    /**
     * Teste: Pode atualizar livro mantendo mesmo ISBN
     */
    public function test_pode_atualizar_livro_mantendo_mesmo_isbn(): void
    {
        $admin = User::factory()->admin()->create();
        $livro = Livro::factory()->create(['isbn' => '978-1-111-11111-1']);
        $autor = Autor::factory()->create();

        $response = $this->actingAs($admin)->put(route('livros.update', $livro), [
            'isbn' => '978-1-111-11111-1', // Mesmo ISBN
            'nome' => 'Nome Atualizado',
            'editora_id' => $livro->editora_id,
            'autores' => [$autor->id],
        ]);

        $response->assertRedirect(route('livros.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Teste: Não pode atualizar com ISBN de outro livro
     */
    public function test_nao_pode_atualizar_com_isbn_de_outro_livro(): void
    {
        $admin = User::factory()->admin()->create();
        $livro1 = Livro::factory()->create(['isbn' => '978-1-111-11111-1']);
        $livro2 = Livro::factory()->create(['isbn' => '978-2-222-22222-2']);
        $autor = Autor::factory()->create();

        $response = $this->actingAs($admin)->put(route('livros.update', $livro2), [
            'isbn' => '978-1-111-11111-1', // ISBN do livro1
            'nome' => $livro2->nome,
            'editora_id' => $livro2->editora_id,
            'autores' => [$autor->id],
        ]);

        $response->assertSessionHasErrors('isbn');
    }

    /**
     * Teste: Admin pode excluir livro
     */
    public function test_admin_pode_excluir_livro(): void
    {
        $admin = User::factory()->admin()->create();
        $livro = Livro::factory()->create();

        $response = $this->actingAs($admin)->delete(route('livros.destroy', $livro));

        $response->assertRedirect(route('livros.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('livros', [
            'id' => $livro->id,
        ]);
    }

    /**
     * Teste: Livro pode ter múltiplos autores
     */
    public function test_livro_pode_ter_multiplos_autores(): void
    {
        $admin = User::factory()->admin()->create();
        $editora = Editora::factory()->create();
        $autor1 = Autor::factory()->create();
        $autor2 = Autor::factory()->create();
        $autor3 = Autor::factory()->create();

        $response = $this->actingAs($admin)->post(route('livros.store'), [
            'isbn' => '978-3-333-33333-3',
            'nome' => 'Livro com Vários Autores',
            'editora_id' => $editora->id,
            'autores' => [$autor1->id, $autor2->id, $autor3->id],
        ]);

        $response->assertRedirect(route('livros.index'));

        $livro = Livro::where('isbn', '978-3-333-33333-3')->first();
        $this->assertCount(3, $livro->autores);
    }

    /**
     * Teste: Preço deve ser numérico e positivo
     */
    public function test_preco_deve_ser_numerico_e_positivo(): void
    {
        $admin = User::factory()->admin()->create();
        $editora = Editora::factory()->create();
        $autor = Autor::factory()->create();

        // Preço negativo
        $response = $this->actingAs($admin)->post(route('livros.store'), [
            'isbn' => '978-4-444-44444-4',
            'nome' => 'Livro Teste',
            'editora_id' => $editora->id,
            'preco' => -10.00,
            'autores' => [$autor->id],
        ]);

        $response->assertSessionHasErrors('preco');
    }
}
