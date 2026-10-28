-- Gestão de Cardápio - Cafeteria (CRUD PHP + MySQLi)

-- Como rodar
1. Coloque a pasta em `htdocs` (XAMPP) e inicie Apache e MySQL.
2. No phpMyAdmin, importe o arquivo `banco.sql`.
3. Ajuste usuário/senha do MySQL em `config.php`, se necessário.
4. Acesse `http://localhost/cafeteria/login.php`.

**Login padrão:** usuário `admin` / senha `admin123`

-- Arquivos
- `login.php` / `logout.php`: autenticação
- `verifica_login.php`: bloqueia páginas para quem não está logado
- `painel.php`: cadastro (Create) e listagem (Read)
- `editar_item.php`: edição (Update)
- `excluir_item.php`: exclusão (Delete) via `?id=X`, com tela de confirmação
- `erro.php`: requisição inválida
