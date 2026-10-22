# Importação do catálogo da costura.pt

```sh
php artisan catalog:import-costura
```

Requer a base de dados migrada, o disco `public` gravável e `public/storage` ligado ao storage. Usa o `ThumbnailService` existente (GD). A migration de `thumb_path` já fazia parte do projeto; o comando recusa iniciar se estiver pendente.

O comando percorre as categorias Corte e Cose e Ponto Preso até encontrar 20 nomes distintos elegíveis em cada uma, incluindo os já existentes; percorre todas as páginas de Agulhas. Reutiliza categorias pelo nome sem distinguir maiúsculas e espaços exteriores. Deduplica produtos por nome limpo + categoria, preservando registos existentes. Uma segunda execução não descarrega novamente fotografias de produtos existentes. Um lock impede execuções simultâneas do importador.

## Dados importados

- Apenas remove o prefixo inicial `[código]` e espaços exteriores do nome.
- Define `price=null`, `negotiable=true`, `status=available`, `featured=false`.
- Não lê nem persiste preços da fonte.
- Reconhece marcas explicitamente presentes no nome, de uma lista conservadora; não infere modelos. Descrição e modelo ficam nulos.
- Nas categorias de máquinas, exclui nomes sem a palavra «máquina» para evitar acessórios misturados na fonte.
- Guarda a imagem principal em `storage/app/public/machines/costura-<uuid>.<ext>` e a thumbnail através do pipeline existente. Define `is_featured=true` apenas na imagem.
- Nunca guarda URLs externas nos campos de imagem.

## HTML verificado

As três listagens usam `form.oe_product_cart`, `a[itemprop=name]`, `img[itemprop=image]` e links de paginação `.page-link`. A navegação encontra páginas adicionais à medida que percorre a categoria; não depende de conhecer antecipadamente a última página. Os detalhes usam `img.product_detail_img`, com `data-zoom-image` a apontar para a imagem principal até 1920 px; as listagens usam 512 px.

Foram encontrados dois placeholders: o Odoo padrão (obtido a cada execução com ID inexistente) e uma imagem genérica personalizada de 512 px. Esta última é rejeitada pelo SHA-256 documentado no parser e tem uma fixture de teste. Se a fonte mudar a imagem genérica, o fingerprint deve ser revisto. Imagens diferentes mas visualmente semelhantes não são consideradas duplicados de produtos: diferentes tamanhos de agulhas podem usar a mesma fotografia legítima.

Liberta ciclos de objetos temporários entre pedidos para limitar o consumo de memória em importações longas. Pedidos sequenciais com intervalo de 500 ms (configurável por `--delay`, mínimo 250 ms fora de testes), timeout de ligação de 10 s e total de 40 s. Redirecionamentos não são seguidos; só são aceites URLs HTTPS da fonte. O idioma português é pedido explicitamente. Há limites de 500 páginas por categoria e 5 MB por imagem, validação de formato e limpeza dos ficheiros se a transação falhar. O comando devolve erro se houver falhas HTTP/processamento ou páginas incompletas, permitindo repetir a execução.

Não altera produtos existentes, mesmo que estejam sem fotografia; nesse caso são ignorados e não contam para a meta. Não há migration nova, coluna de origem ou dependência adicional. A identificação por nome/categoria pressupõe que o nome da fonte não seja alterado posteriormente.

## Verificação

```sh
php artisan test
```

Os testes usam HTTP e storage fakes e uma base SQLite em memória. Cobrem limpeza de nomes, marcas, placeholders, falhas HTTP, paginação cíclica, categorias, preço/estado, imagens e thumbnails, apresentação pública, deduplicação e o limite de 20 numa repetição.
