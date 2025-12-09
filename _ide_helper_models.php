<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Livro> $livros
 * @property-read int|null $livros_count
 * @method static \Database\Factories\AutorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereUpdatedAt($value)
 */
	class Autor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $livro_id
 * @property bool $notificado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Livro $livro
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert doLivro($livroId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert doUtilizador($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert naoNotificados()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert whereLivroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert whereNotificado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityAlert whereUserId($value)
 */
	class AvailabilityAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $livro_id
 * @property int $quantidade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Livro $livro
 * @property-read float $subtotal
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo user()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo livro()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem whereLivroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem whereQuantidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrinhoItem whereUserId($value)
 */
	class CarrinhoItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property string|null $logotipo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Livro> $livros
 * @property-read int|null $livros_count
 * @method static \Database\Factories\EditoraFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereLogotipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereUpdatedAt($value)
 */
	class Editora extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $numero_encomenda
 * @property string $nome_completo
 * @property string $email
 * @property string|null $telefone
 * @property string $morada
 * @property string $cidade
 * @property string $codigo_postal
 * @property string $pais
 * @property float $subtotal
 * @property float $taxas
 * @property float $total
 * @property string $estado
 * @property string|null $stripe_payment_intent_id
 * @property string|null $notas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read Collection<int, \App\Models\EncomendaItem> $items
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo user()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany items()
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereCidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereMorada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereNomeCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereNotas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereNumeroEncomenda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda wherePais($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereTaxas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Encomenda whereUserId($value)
 */
	class Encomenda extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $encomenda_id
 * @property int $livro_id
 * @property int $quantidade
 * @property numeric $preco_unitario
 * @property numeric $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Encomenda $encomenda
 * @property-read \App\Models\Livro $livro
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereEncomendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereLivroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem wherePrecoUnitario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereQuantidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EncomendaItem whereUpdatedAt($value)
 */
	class EncomendaItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $isbn
 * @property string $nome
 * @property int $editora_id
 * @property string|null $bibliografia
 * @property string|null $imagem_capa
 * @property float $preco
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Editora $editora
 * @property-read Collection<int, \App\Models\Autor> $autores
 * @property-read Collection<int, \App\Models\Requisicao> $requisicoes
 * @property-read Collection<int, \App\Models\Review> $reviews
 * @property-read Collection<int, \App\Models\AvailabilityAlert> $availabilityAlerts
 * @property-read Collection<int, \App\Models\CarrinhoItem> $carrinhoItems
 * @property-read Collection<int, \App\Models\EncomendaItem> $encomendaItems
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo editora()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany autores()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany requisicoes()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reviews()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany availabilityAlerts()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany carrinhoItems()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany encomendaItems()
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property-read int|null $autores_count
 * @property-read int|null $availability_alerts_count
 * @property-read int|null $carrinho_items_count
 * @property-read int|null $encomenda_items_count
 * @property-read int|null $requisicoes_count
 * @property-read int|null $reviews_count
 * @method static \Database\Factories\LivroFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereBibliografia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereEditoraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereImagemCapa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereIsbn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereLastAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro wherePreco($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereUpdatedAt($value)
 */
	class Livro extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $livro_id
 * @property string $estado
 * @property \Illuminate\Support\Carbon $data_requisicao
 * @property \Illuminate\Support\Carbon|null $data_prevista_devolucao
 * @property \Illuminate\Support\Carbon|null $data_devolucao
 * @property string|null $observacoes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $foto_cidadao
 * @property \Illuminate\Support\Carbon|null $data_recepcao
 * @property int|null $recebido_por
 * @property-read \App\Models\Livro $livro
 * @property-read \App\Models\User|null $recebidoPor
 * @property-read \App\Models\Review|null $review
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao ativas()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao doLivro($livroId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao doUtilizador($userId)
 * @method static \Database\Factories\RequisicaoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao passadas()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao recentes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereDataDevolucao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereDataPrevistaDevolucao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereDataRecepcao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereDataRequisicao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereFotoCidadao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereLivroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereObservacoes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereRecebidoPor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereUserId($value)
 */
	class Requisicao extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $livro_id
 * @property int $requisicao_id
 * @property string $comentario
 * @property string $estado
 * @property string|null $justificacao_recusa
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Livro $livro
 * @property-read \App\Models\Requisicao $requisicao
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review ativos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review doLivro($livroId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review doUtilizador($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review recusados()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review suspensos()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComentario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereJustificacaoRecusa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereLivroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRequisicaoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\Requisicao> $requisicoes
 * @property-read Collection<int, \App\Models\Review> $reviews
 * @property-read Collection<int, \App\Models\AvailabilityAlert> $availabilityAlerts
 * @property-read Collection<int, \App\Models\CarrinhoItem> $carrinhoItems
 * @property-read Collection<int, \App\Models\Encomenda> $encomendas
 * @method \Illuminate\Database\Eloquent\Relations\HasMany requisicoes()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reviews()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany availabilityAlerts()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany carrinhoItems()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany encomendas()
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property-read int|null $availability_alerts_count
 * @property-read int|null $carrinho_items_count
 * @property-read int|null $encomendas_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read string $profile_photo_url
 * @property-read int|null $requisicoes_count
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

