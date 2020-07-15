-- view_affluenze source

CREATE OR REPLACE VIEW view_affluenze
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.circ_desc,
    vs.descrizione AS sezione,
    vs.numero,
    vs.stato_wf,
    cxv.comunicazione_desc,
    rxv.id,
    rxv.rxsezione_id,
    rxv.confxvotanti_id,
    rxv.num_votanti_maschi,
    rxv.num_votanti_femmine,
    rxv.num_votanti_totali,
    rxv.off,
    rxv."timestamp",
    rxv.sent,
    rxv.discr
   FROM rxvotanti rxv,
    confxvotanti cxv,
    view_sezioni vs
  WHERE cxv.id = rxv.confxvotanti_id AND cxv.evento_id = vs.evento_id AND vs.id = rxv.rxsezione_id AND rxv.off IS NOT TRUE AND cxv.off IS NOT TRUE
  ORDER BY vs.numero;


-- view_enabledsend_scrutini source

CREATE OR REPLACE VIEW view_enabledsend_scrutini
AS SELECT r.rxsezione_id
   FROM rxscrutinicandidati r,
    rxvotinonvalidi r3,
    rxvotanti r4,
    confxvotanti c
  WHERE r.off IS NOT TRUE AND r3.off IS NOT TRUE AND r4.off IS NOT TRUE AND c.off IS NOT TRUE AND c.comunicazione_final = true AND r4.confxvotanti_id = c.id AND r.rxsezione_id = r3.rxsezione_id AND r.rxsezione_id = r4.rxsezione_id
  GROUP BY r.rxsezione_id
  ORDER BY r.rxsezione_id;


-- view_scrutini_candidatoprincipale source

CREATE OR REPLACE VIEW view_scrutini_candidatoprincipale
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.numero,
    vs.circo_id,
    vs.circ_desc,
    vs.descrizione AS sezione,
    cand.cognome,
    cand.nome,
    cand.luogo_nascita,
    cand.sesso,
    cand.id_source,
    scrutini.id,
    scrutini.rxsezione_id,
    scrutini.candidato_principale_id,
    scrutini.voti_totale_candidato,
    scrutini.voti_dicui_solo_candidato,
    scrutini.off,
    scrutini."timestamp",
    scrutini.sent,
    scrutini.discr
   FROM rxscrutinicandidati scrutini,
    candidatiprincipali cand,
    view_sezioni vs
  WHERE cand.id = scrutini.candidato_principale_id AND scrutini.rxsezione_id = vs.id AND scrutini.off IS NOT TRUE AND cand.off IS NOT TRUE
  ORDER BY vs.numero;


-- view_scrutini_candidatosecondario source

CREATE OR REPLACE VIEW view_scrutini_candidatosecondario
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.numero,
    vs.circo_id,
    vs.descrizione AS sezione,
    candsec.cognome,
    candsec.nome,
    candsec.luogo_nascita,
    candsec.sesso,
    candsec.id_source,
    l.lista_desc,
    sxl.posizione,
    preferenze.id,
    preferenze.rxsezione_id,
    preferenze.listapreferenze_id,
    preferenze.candidato_secondario_id,
    preferenze.off,
    preferenze."timestamp",
    preferenze.sent,
    preferenze.numero_voti,
    preferenze.discr
   FROM rxpreferenze preferenze,
    candidatisecondari candsec,
    secondarioxlista sxl,
    listapreferenze l,
    view_sezioni vs
  WHERE preferenze.candidato_secondario_id = candsec.id AND sxl.candidato_secondario_id = candsec.id AND preferenze.listapreferenze_id = sxl.lista_id AND sxl.lista_id = l.id AND preferenze.rxsezione_id = vs.id AND preferenze.off IS NOT TRUE AND candsec.off IS NOT TRUE AND sxl.off IS NOT TRUE AND l.off IS NOT TRUE
  ORDER BY vs.numero, l.id, sxl.posizione;


-- view_scrutini_liste source

CREATE OR REPLACE VIEW view_scrutini_liste
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.circ_desc,
    vs.circo_id,
    vs.numero,
    vs.descrizione AS sezione,
    l.lista_desc,
    l.id_source,
    scrutiniliste.id,
    scrutiniliste.lista_preferenze_id,
    scrutiniliste.rxsezione_id,
    scrutiniliste.voti_tot_lista,
    scrutiniliste.off,
    scrutiniliste."timestamp",
    scrutiniliste.sent,
    scrutiniliste.discr
   FROM rxscrutiniliste scrutiniliste,
    listaxprincipale listaxprincipale,
    listapreferenze l,
    view_sezioni vs
  WHERE scrutiniliste.lista_preferenze_id = listaxprincipale.lista_id AND listaxprincipale.lista_id = l.id AND scrutiniliste.rxsezione_id = vs.id AND scrutiniliste.off IS NOT TRUE AND listaxprincipale.off IS NOT TRUE AND l.off IS NOT TRUE
  ORDER BY vs.numero, (l.id_source::integer);


-- view_scrutini_votinulli source

CREATE OR REPLACE VIEW view_scrutini_votinulli
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.circ_desc,
    vs.descrizione AS sezione,
    rnon.id,
    rnon.rxsezione_id,
    rnon.numero_schede_bianche,
    rnon.numero_schede_nulle,
    rnon.numero_schede_contestate,
    rnon.tot_voti_dicui_solo_candidato,
    rnon.voti_nulli_liste,
    rnon.voti_nulli_coalizioni,
    rnon.voti_contestati_liste,
    rnon.off,
    rnon."timestamp",
    rnon.sent,
    rnon.discr
   FROM rxvotinonvalidi rnon,
    view_sezioni vs
  WHERE rnon.rxsezione_id = vs.id AND rnon.off IS NOT TRUE
  ORDER BY vs.numero;


-- view_sezioni source

CREATE OR REPLACE VIEW view_sezioni
AS SELECT e.ente_id,
    c.evento_id,
    c.circ_desc,
    r.id,
    r.circo_id,
    r.numero,
    r.descrizione,
    r.discr,
    r.stato_wf
   FROM entexevento e,
    rxsezioni r,
    circoscrizioni c,
    eventi eventi
  WHERE c.evento_id = e.evento_id AND r.circo_id = c.id AND eventi.id = e.evento_id AND e.off IS NOT TRUE AND c.off IS NOT TRUE AND eventi.off IS NOT TRUE
  ORDER BY r.numero;