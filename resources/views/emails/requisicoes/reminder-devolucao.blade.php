<!DOCTYPE html>

<html lang="pt">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lembrete: Devolução de Livro Amanhã</title>

</head>

    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">📚 Lembrete de Devolução</h1>
        </div>

        <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px;">

            <p style="font-size: 16px; margin-bottom: 20px;">
                Olá <strong>{{ $requisicao->user->name }}</strong>,
            </p>

            <p style="font-size: 16px; background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                ⚠️ Este é um lembrete de que o prazo de devolução do livro que requisitou termina <strong>amanhã</strong>.
            </p>

            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                <h2 style="color: #667eea; margin-top: 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                    📖 Detalhes do Livro
                </h2>

                <table style="width: 100%; margin-top: 15px;">

                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Livro:</td>
                        <td style="padding: 8px 0;">{{ $requisicao->livro->nome }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">ISBN:</td>
                        <td style="padding: 8px 0;">{{ $requisicao->livro->isbn }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Autor(es):</td>
                        <td style="padding: 8px 0;">{{ $requisicao->livro->autores->pluck('nome')->join(', ') }}</td>
                    </tr>
                </table>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                <h2 style="color: #667eea; margin-top: 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                    📋 Informações da Requisição
                </h2>

                <table style="width: 100%; margin-top: 15px;">

                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Data da Requisição:</td>
                        <td style="padding: 8px 0;">{{ $requisicao->data_requisicao->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Data Prevista Devolução:</td>
                        <td style="padding: 8px 0; color: #d9534f; font-weight: bold;">{{ $requisicao->data_prevista_devolucao->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Estado:</td>
                        <td style="padding: 8px 0;">
                            <span style="background: #5cb85c; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                {{ ucfirst($requisicao->estado) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 15px; color: #666; margin: 25px 0;">
                Por favor, não se esqueça de devolver o livro até à data prevista para evitar atrasos.
            </p>

            <div style="text-align: center; margin: 30px 0;">

                <a href="{{ route('requisicoes.index') }}"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

                        color: white;
                        padding: 14px 30px;
                        text-decoration: none;
                        border-radius: 25px;
                        display: inline-block;
                        font-weight: bold;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    📚 Ver as Minhas Requisições
                </a>
            </div>

            <p style="font-size: 14px; color: #888; margin-top: 30px;">
                Agradecemos a sua colaboração!
            </p>

            <hr style="border: none; border-top: 1px solid #ddd; margin: 25px 0;">
            <p style="font-size: 14px; color: #888; margin: 0;">
                Cumprimentos,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </div>

        <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">

            <p style="margin: 5px 0;">© {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
            <p style="margin: 5px 0;">Este é um email automático, por favor não responda.</p>

        </div>
    </body>
</html>
