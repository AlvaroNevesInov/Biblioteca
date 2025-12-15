<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogService
{
    /**
     * Registrar uma ação no sistema de logs
     *
     * @param string $modulo O módulo/área da aplicação (ex: 'Livros', 'Autores', 'Requisições')
     * @param string $acao A ação realizada (ex: 'Criar', 'Editar', 'Eliminar', 'Aprovar')
     * @param int|null $objectId ID do objeto relacionado (opcional)
     * @param string|null $descricao Descrição detalhada da ação (opcional)
     * @param int|null $userId ID do usuário (se null, usa o usuário autenticado)
     * @return Log
     */
    public static function log(
        string $modulo,
        string $acao,
        ?int $objectId = null,
        ?string $descricao = null,
        ?int $userId = null
    ): Log {
        return Log::create([
            'user_id' => $userId ?? Auth::id(),
            'modulo' => $modulo,
            'acao' => $acao,
            'object_id' => $objectId,
            'descricao' => $descricao,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Registrar criação de um recurso
     */
    public static function logCreate(string $modulo, int $objectId, ?string $descricao = null): Log
    {
        return self::log($modulo, 'Criar', $objectId, $descricao);
    }

    /**
     * Registrar atualização de um recurso
     */
    public static function logUpdate(string $modulo, int $objectId, ?string $descricao = null): Log
    {
        return self::log($modulo, 'Atualizar', $objectId, $descricao);
    }

    /**
     * Registrar eliminação de um recurso
     */
    public static function logDelete(string $modulo, int $objectId, ?string $descricao = null): Log
    {
        return self::log($modulo, 'Eliminar', $objectId, $descricao);
    }

    /**
     * Registrar visualização de um recurso
     */
    public static function logView(string $modulo, int $objectId, ?string $descricao = null): Log
    {
        return self::log($modulo, 'Visualizar', $objectId, $descricao);
    }

    /**
     * Registrar login de usuário
     */
    public static function logLogin(?int $userId = null): Log
    {
        return self::log('Autenticação', 'Login', $userId, 'Utilizador fez login no sistema', $userId);
    }

    /**
     * Registrar logout de usuário
     */
    public static function logLogout(?int $userId = null): Log
    {
        return self::log('Autenticação', 'Logout', $userId, 'Utilizador fez logout do sistema', $userId);
    }

    /**
     * Registrar ação personalizada
     */
    public static function logCustom(string $modulo, string $acao, ?string $descricao = null): Log
    {
        return self::log($modulo, $acao, null, $descricao);
    }
}
