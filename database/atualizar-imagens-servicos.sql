-- Execute no banco wincar existente. Atualiza somente as imagens dos servicos.
SET NAMES utf8mb4;
START TRANSACTION;
UPDATE servico SET imagem = 'externa.png' WHERE nome = 'Lavagem Externa';
UPDATE servico SET imagem = 'externaaspiração.png' WHERE nome = 'Lavagem + Aspiração';
UPDATE servico SET imagem = 'completa.png' WHERE nome = 'Lavagem Completa';
UPDATE servico SET imagem = 'completahigienizacao.png' WHERE nome = 'Lavagem Completa + Higienização Interna';
UPDATE servico SET imagem = 'lavagemenceramento.png' WHERE nome = 'Lavagem + Enceramento';
UPDATE servico SET imagem = 'tecnica.png' WHERE nome = 'Lavagem Técnica';
UPDATE servico SET imagem = 'polimentotec.png' WHERE nome = 'Polimento Técnico';
UPDATE servico SET imagem = 'protecaopintura.png' WHERE nome = 'Polimento + Proteção de Pintura';
UPDATE servico SET imagem = 'detailing.png' WHERE nome = 'Detailing Completo';
COMMIT;
