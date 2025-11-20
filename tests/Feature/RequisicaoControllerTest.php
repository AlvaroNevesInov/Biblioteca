<?php

namespace Tests\Feature;

use App\Mail\NovaRequisicaoAdmin;
use App\Mail\NovaRequisicaoCidadao;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RequisicaoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Cidadão pode visualizar listagem de requisições
     */
    public function test_cidadao_pode_visualizar_listagem_requisicoes(): void
    {
        $user = User::factory()->cidadao()->create();

        $response = $this->actingAs($user)->get(route('requisicoes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('requisicoes.index');
    }

    /**
     * Teste: Cidadão pode acessar formulário de criação se tiver menos de 3 requisições
     */
    public function test_cidadao_pode_acessar_formulario_criacao(): void
    {
        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $response = $this->actingAs($user)->get(route('requisicoes.create', ['livro_id' => $livro->id]));

        $response->assertStatus(200);
        $response->assertViewIs('requisicoes.create');
        $response->assertViewHas('livro');
    }

    /**
     * Teste: Cidadão não pode acessar formulário se atingiu limite de 3 livros
     */
    public function test_cidadao_nao_pode_acessar_formulario_se_atingiu_limite(): void
    {
        $user = User::factory()->cidadao()->create();

        // Criar 3 requisições ativas
        for ($i = 0; $i < 3; $i++) {
            Requisicao::factory()->aprovada()->create(['user_id' => $user->id]);
        }

        $response = $this->actingAs($user)->get(route('requisicoes.create'));

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Teste: Cidadão pode criar requisição com sucesso
     */
    public function test_cidadao_pode_criar_requisicao_com_sucesso(): void
    {
        Mail::fake();
        Storage::fake('public');

        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();
        $admin = User::factory()->admin()->create();

        $foto = UploadedFile::fake()->image('foto.jpg', 800, 600);

        $response = $this->actingAs($user)->post(route('requisicoes.store'), [
            'livro_id' => $livro->id,
            'foto_cidadao' => $foto,
            'observacoes' => 'Preciso deste livro urgentemente',
        ]);

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('requisicoes', [
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
        ]);

        // Verificar que emails foram enviados
        Mail::assertQueued(NovaRequisicaoCidadao::class);
        Mail::assertQueued(NovaRequisicaoAdmin::class);
    }

    /**
     * Teste: Cidadão não pode criar requisição sem foto
     */
    public function test_cidadao_nao_pode_criar_requisicao_sem_foto(): void
    {
        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $response = $this->actingAs($user)->post(route('requisicoes.store'), [
            'livro_id' => $livro->id,
            'observacoes' => 'Teste',
        ]);

        $response->assertSessionHasErrors('foto_cidadao');
    }

    /**
     * Teste: Cidadão não pode criar requisição se livro já está requisitado
     */
    public function test_cidadao_nao_pode_requisitar_livro_ja_requisitado(): void
    {
        Mail::fake();

        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        // Criar requisição ativa para o livro
        Requisicao::factory()->aprovada()->create(['livro_id' => $livro->id]);

        $foto = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($user)->post(route('requisicoes.store'), [
            'livro_id' => $livro->id,
            'foto_cidadao' => $foto,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Teste: Cidadão não pode criar mais de 3 requisições ativas
     */
    public function test_cidadao_nao_pode_criar_mais_de_3_requisicoes(): void
    {
        $user = User::factory()->cidadao()->create();

        // Criar 3 requisições ativas
        for ($i = 0; $i < 3; $i++) {
            Requisicao::factory()->aprovada()->create(['user_id' => $user->id]);
        }

        $livro = Livro::factory()->create();
        $foto = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($user)->post(route('requisicoes.store'), [
            'livro_id' => $livro->id,
            'foto_cidadao' => $foto,
        ]);

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Teste: Admin pode aprovar requisição
     */
    public function test_admin_pode_aprovar_requisicao(): void
    {
        $admin = User::factory()->admin()->create();
        $requisicao = Requisicao::factory()->create(['estado' => 'pendente']);

        $response = $this->actingAs($admin)->patch(route('requisicoes.aprovar', $requisicao));

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'estado' => 'aprovada',
        ]);
    }

    /**
     * Teste: Cidadão não pode aprovar requisição
     */
    public function test_cidadao_nao_pode_aprovar_requisicao(): void
    {
        $cidadao = User::factory()->cidadao()->create();
        $requisicao = Requisicao::factory()->create(['estado' => 'pendente']);

        $response = $this->actingAs($cidadao)->patch(route('requisicoes.aprovar', $requisicao));

        $response->assertStatus(403);
    }

    /**
     * Teste: Admin pode rejeitar requisição
     */
    public function test_admin_pode_rejeitar_requisicao(): void
    {
        $admin = User::factory()->admin()->create();
        $requisicao = Requisicao::factory()->create(['estado' => 'pendente']);

        $response = $this->actingAs($admin)->patch(route('requisicoes.rejeitar', $requisicao), [
            'observacoes' => 'Livro não disponível',
        ]);

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'estado' => 'rejeitada',
        ]);
    }

    /**
     * Teste: Admin pode marcar como devolvida
     */
    public function test_admin_pode_marcar_como_devolvida(): void
    {
        $admin = User::factory()->admin()->create();
        $requisicao = Requisicao::factory()->aprovada()->create();

        $response = $this->actingAs($admin)->patch(route('requisicoes.devolver', $requisicao));

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'estado' => 'devolvida',
        ]);
    }

    /**
     * Teste: Admin pode confirmar recepção do livro
     */
    public function test_admin_pode_confirmar_recepcao(): void
    {
        $admin = User::factory()->admin()->create();
        $requisicao = Requisicao::factory()->devolvida()->create();

        $response = $this->actingAs($admin)->patch(route('requisicoes.confirmar-recepcao', $requisicao), [
            'data_recepcao' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $requisicao->refresh();
        $this->assertNotNull($requisicao->data_recepcao);
        $this->assertEquals($admin->id, $requisicao->recebido_por);
    }

    /**
     * Teste: Cidadão pode cancelar sua própria requisição pendente
     */
    public function test_cidadao_pode_cancelar_requisicao_pendente(): void
    {
        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $response = $this->actingAs($user)->delete(route('requisicoes.destroy', $requisicao->id));

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('requisicoes', [
            'id' => $requisicao->id,
        ]);
    }

    /**
     * Teste: Cidadão não pode cancelar requisição aprovada
     */
    public function test_cidadao_nao_pode_cancelar_requisicao_aprovada(): void
    {
        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $response = $this->actingAs($user)->delete(route('requisicoes.destroy', $requisicao->id));

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
        ]);
    }

    /**
     * Teste: Cidadão não pode cancelar requisição de outro utilizador
     */
    public function test_cidadao_nao_pode_cancelar_requisicao_de_outro(): void
    {
        $user1 = User::factory()->cidadao()->create();
        $user2 = User::factory()->cidadao()->create();
        $requisicao = Requisicao::factory()->create([
            'user_id' => $user2->id,
            'estado' => 'pendente',
        ]);

        $response = $this->actingAs($user1)->delete(route('requisicoes.destroy', $requisicao->id));

        $response->assertStatus(403);
    }

     /**
     * Teste: Admin pode atualizar requisição
     */
    public function test_admin_pode_atualizar_requisicao(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $dataPrevista = now()->addDays(7)->format('Y-m-d');

        $response = $this->actingAs($admin)->put(route('requisicoes.update', $requisicao), [
            'estado' => 'aprovada',
            'data_prevista_devolucao' => $dataPrevista,
            'observacoes' => 'Aprovada após análise',
        ]);

        $response->assertRedirect(route('requisicoes.index'));
        $response->assertSessionHas('success');

        $requisicao->refresh();
        $this->assertEquals('aprovada', $requisicao->estado);
        $this->assertEquals($dataPrevista, $requisicao->data_prevista_devolucao->format('Y-m-d'));
    }

}
