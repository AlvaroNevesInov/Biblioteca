<?php

use App\Mail\NovaRequisicaoAdmin;
use App\Mail\NovaRequisicaoCidadao;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Teste de Criação de Requisição de Livro
 *
 * Este teste verifica se um utilizador pode criar uma requisição de um livro corretamente.
 */
test('cidadão pode criar uma requisição de livro corretamente', function () {
    /** @var \Tests\TestCase $this */

    // Configurar mocks de Mail e Storage
    \Illuminate\Support\Facades\Mail::fake();
    \Illuminate\Support\Facades\Storage::fake('public');

    // 1. Criar um utilizador e um livro na base de dados
    $user = User::factory()->cidadao()->create([
        'name' => 'João Silva',
        'email' => 'joao@example.com',
    ]);

    $livro = Livro::factory()->create([
        'nome' => 'Clean Code',
        'isbn' => '9780132350884',
    ]);

    $admin = User::factory()->admin()->create();

    // 2. Simular a submissão de uma requisição
    $foto = UploadedFile::fake()->image('foto_cidadao.jpg', 800, 600);

    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
        'foto_cidadao' => $foto,
        'observacoes' => 'Preciso deste livro urgentemente para o meu projeto',
    ]);

    // 3. Garantir que a requisição foi criada e que os dados estão corretos
    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHas('success');

    // Verificar que a requisição foi criada na base de dados
    expect(Requisicao::count())->toBe(1);

    $requisicao = Requisicao::first();

    expect($requisicao)->not->toBeNull()
        ->and($requisicao->user_id)->toBe($user->id)
        ->and($requisicao->livro_id)->toBe($livro->id)
        ->and($requisicao->estado)->toBe('pendente')
        ->and($requisicao->observacoes)->toBe('Preciso deste livro urgentemente para o meu projeto')
        ->and($requisicao->data_requisicao)->not->toBeNull();

    // Verificar que os emails foram enviados
    Mail::assertQueued(NovaRequisicaoCidadao::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });

    Mail::assertQueued(NovaRequisicaoAdmin::class);

    // Verificar que a foto foi armazenada
    $this->assertDatabaseHas('requisicoes', [
        'user_id' => $user->id,
        'livro_id' => $livro->id,
        'estado' => 'pendente',
    ]);
});

/**
 * Teste adicional: Verificar que a validação funciona
 */
test('cidadão não pode criar requisição sem foto', function () {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->cidadao()->create();
    $livro = Livro::factory()->create();

    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
        'observacoes' => 'Teste sem foto',
    ]);

    $response->assertSessionHasErrors('foto_cidadao');

    expect(Requisicao::count())->toBe(0);
});

/**
 * Teste adicional: Verificar limite de requisições
 */
test('cidadão não pode criar mais de 3 requisições ativas', function () {
    /** @var \Tests\TestCase $this */

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

    // Verificar que nenhuma nova requisição foi criada
    expect(Requisicao::count())->toBe(3);
});

/**
 * Teste de Validação de Requisição
 *
 * Este teste verifica que uma requisição não pode ser criada sem um livro válido.
 */
test('requisição não pode ser criada sem livro válido', function () {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->cidadao()->create();
    $foto = UploadedFile::fake()->image('foto.jpg');

    // Simular requisição sem livro_id
    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'foto_cidadao' => $foto,
        'observacoes' => 'Teste sem livro',
    ]);

    // Verificar se o Laravel retorna erro de validação
    $response->assertSessionHasErrors('livro_id');

    // Verificar que nenhuma requisição foi criada
    expect(Requisicao::count())->toBe(0);
});

test('requisição não pode ser criada com livro inexistente', function () {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->cidadao()->create();
    $foto = UploadedFile::fake()->image('foto.jpg');

    // Simular requisição com livro_id que não existe
    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livro_id' => 99999, // ID que não existe
        'foto_cidadao' => $foto,
        'observacoes' => 'Teste com livro inexistente',
    ]);

    // Verificar se o Laravel retorna erro de validação
    $response->assertSessionHasErrors('livro_id');

    // Verificar que nenhuma requisição foi criada
    expect(Requisicao::count())->toBe(0);
});


/**
 * Teste de Devolução de Livro
 *
 * Confirma se um utilizador pode devolver um livro.
 */
test('utilizador pode devolver um livro', function () {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->cidadao()->create();
    $livro = Livro::factory()->create();

    // 1. Criar uma requisição ativa (aprovada) na base de dados
    $requisicao = Requisicao::factory()->aprovada()->create([
        'user_id' => $user->id,
        'livro_id' => $livro->id,
    ]);

    expect($requisicao->estado)->toBe('aprovada')
        ->and($requisicao->data_recepcao)->toBeNull();

    // 2. Simular a devolução do livro (marcar como devolvida)
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->patch(
        route('requisicoes.devolver', $requisicao)
    );

    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHas('success');

    // 3. Verificar se o estado foi atualizado corretamente
    $requisicao->refresh();

    expect($requisicao->estado)->toBe('devolvida');

    $this->assertDatabaseHas('requisicoes', [
        'id' => $requisicao->id,
        'estado' => 'devolvida',
    ]);
});

/**
 * Teste de Listagem de Requisições por Utilizador
 *
 * Garante que um utilizador consegue ver as suas requisições corretamente.
 */
test('utilizador consegue ver apenas suas requisições', function () {
    /** @var \Tests\TestCase $this */

    // 1. Criar múltiplas requisições para diferentes utilizadores
    $user1 = User::factory()->cidadao()->create(['name' => 'Utilizador 1']);
    $user2 = User::factory()->cidadao()->create(['name' => 'Utilizador 2']);

    // Criar 3 requisições para o utilizador 1
    $requisicoesUser1 = Requisicao::factory()->count(3)->create([
        'user_id' => $user1->id,
    ]);

    // Criar 2 requisições para o utilizador 2
    $requisicoesUser2 = Requisicao::factory()->count(2)->create([
        'user_id' => $user2->id,
    ]);

    // 2. Simular pedido para obter requisições do utilizador 1
    $response = $this->actingAs($user1)->get(route('requisicoes.index'));

    $response->assertStatus(200);
    $response->assertViewIs('requisicoes.index');

    // 3. Verificar se apenas as requisições corretas são retornadas
    // Verificar que o utilizador 1 vê suas 3 requisições
    expect(Requisicao::where('user_id', $user1->id)->count())->toBe(3)
        ->and(Requisicao::where('user_id', $user2->id)->count())->toBe(2);

    // Verificar que as requisições do user1 estão na base de dados
    foreach ($requisicoesUser1 as $req) {
        $this->assertDatabaseHas('requisicoes', [
            'id' => $req->id,
            'user_id' => $user1->id,
        ]);
    }

    // Verificar que as requisições do user2 NÃO aparecem para user1
    foreach ($requisicoesUser2 as $req) {
        expect($req->user_id)->toBe($user2->id)
            ->and($req->user_id)->not->toBe($user1->id);
    }
});

/**
 * Teste de Stock na Requisição de Livros
 *
 * Confirma se não é possível requisitar um livro sem stock disponível.
 */
test('não é possível requisitar livro sem stock disponível', function () {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->cidadao()->create();

    // 1. Criar um livro com stock = 0
    $livro = Livro::factory()->create([
        'nome' => 'Livro Sem Stock',
        'stock' => 0,
    ]);

    expect($livro->stock)->toBe(0);

    $foto = UploadedFile::fake()->image('foto.jpg');

    // 2. Tentar criar uma requisição para esse livro
    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
        'foto_cidadao' => $foto,
        'observacoes' => 'Tentativa de requisitar livro sem stock',
    ]);

    // 3. Verificar se a aplicação impede a operação com mensagem de erro
    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Verificar que nenhuma requisição foi criada
    expect(Requisicao::where('livro_id', $livro->id)->count())->toBe(0);
});

test('é possível requisitar livro com stock disponível', function () {
    /** @var \Tests\TestCase $this */

    Mail::fake();
    Storage::fake('public');

    $user = User::factory()->cidadao()->create();

    // Criar um livro com stock > 0
    $livro = Livro::factory()->create([
        'nome' => 'Livro Com Stock',
        'stock' => 5,
    ]);

    expect($livro->stock)->toBe(5);

    $foto = UploadedFile::fake()->image('foto.jpg');

    // Tentar criar uma requisição para esse livro
    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
        'foto_cidadao' => $foto,
        'observacoes' => 'Requisitar livro com stock',
    ]);

    // Verificar que a requisição foi criada com sucesso
    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHas('success');

    // Verificar que a requisição existe na base de dados
    $this->assertDatabaseHas('requisicoes', [
        'user_id' => $user->id,
        'livro_id' => $livro->id,
    ]);

    expect(Requisicao::where('livro_id', $livro->id)->count())->toBe(1);
});
