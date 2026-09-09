----------------------------------------
----------------------------------------
---- ENCUESTA SATISFACCION WIENER FEST -
----------------------------------------
----------------------------------------

/*
MAPEO SUGERIDO DE RESPUESTAS

q1_conocimiento_antes
1 = No la conocia para nada
2 = La habia escuchado pero no sabia mucho
3 = La conocia mas o menos
4 = La conocia muy bien, ya la estaba considerando

q2_conocimiento_despues
1 = Igual que antes
2 = La conozco un poco mas
3 = La conozco bastante mejor
4 = Ahora la conozco muy bien

q3_gusto_mas
Guardar codigos separados por coma.
Valores sugeridos:
stands,docentes,instalaciones,info_carreras,talleres,alumnos,ambiente,organizacion

q4_sentimiento_general
1 = Mala
2 = Regular
3 = Bien
4 = Muy bien
5 = Genial

q5_taller_general / q5_tallerista / q5_aprendio_nuevo
NULL = No asistio a taller
1..5 = Calificacion

q6_nps
0..10 = Recomendacion

q7_intencion_estudiar
1 = Si, ya tome la decision
2 = Si, es una de mis opciones
3 = Lo estoy evaluando
4 = Por ahora no
5 = Ya soy alumno/a o egresado/a

q9_como_se_entero
1 = Redes sociales
2 = WhatsApp o mensaje
3 = Amigo o familiar
4 = Colegio o instituto
5 = Email
6 = Me avisaron en la U

q10_situacion_actual
1 = En el colegio (4to o 5to)
2 = Egrese, aun no estudio
3 = Estudio en otra universidad
4 = Trabajo o soy profesional
5 = Soy alumno/a de Wiener
6 = Soy padre o madre
*/

CREATE TABLE `encuesta_wiener_fest` (
  `idencuesta` int(11) NOT NULL,
  `idcampania` int(11) DEFAULT NULL,
  `idevento` int(11) DEFAULT NULL,
  `q1_conocimiento_antes` tinyint(1) NOT NULL,
  `q2_conocimiento_despues` tinyint(1) NOT NULL,
  `q3_gusto_mas` varchar(500) NOT NULL,
  `q4_sentimiento_general` tinyint(1) NOT NULL,
  `q5_taller_general` tinyint(1) DEFAULT NULL,
  `q5_tallerista` tinyint(1) DEFAULT NULL,
  `q5_aprendio_nuevo` tinyint(1) DEFAULT NULL,
  `q6_nps` tinyint(2) NOT NULL,
  `q7_intencion_estudiar` tinyint(1) NOT NULL,
  `q8_que_cambiarias` text NOT NULL,
  `q9_como_se_entero` tinyint(1) NOT NULL,
  `q10_situacion_actual` tinyint(1) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `ip_hash` varchar(64) DEFAULT NULL,
  `user_agent_hash` varchar(64) DEFAULT NULL,
  `origen_qr` varchar(255) DEFAULT NULL,
  `userinsert` varchar(60) DEFAULT 'encuesta_publica',
  `dateinsert` timestamp NOT NULL DEFAULT current_timestamp(),
  `userupdate` varchar(60) DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `encuesta_wiener_fest`
  ADD PRIMARY KEY (`idencuesta`),
  ADD KEY `idx_encuesta_idcampania` (`idcampania`),
  ADD KEY `idx_encuesta_idevento` (`idevento`),
  ADD KEY `idx_encuesta_dateinsert` (`dateinsert`),
  ADD KEY `idx_encuesta_estado` (`estado`);

ALTER TABLE `encuesta_wiener_fest`
  MODIFY `idencuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

----------------------------------------
----------------------------------------
---- REPORTES ENCUESTA WIENER FEST -----
----------------------------------------
----------------------------------------

/*
Ajusta filtros antes de ejecutar:
- @idcampania: NULL para todas
- @idevento: NULL para todos
- @desde y @hasta en formato YYYY-MM-DD
*/

SET @idcampania = NULL;
SET @idevento = NULL;
SET @desde = NULL;
SET @hasta = NULL;


-- 1) Cuantas personas realizaron la encuesta
SELECT COUNT(*) AS total_encuestas
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta);

-- 2) Distribucion pregunta 1
SELECT e.q1_conocimiento_antes AS opcion, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q1_conocimiento_antes
ORDER BY e.q1_conocimiento_antes;

-- 3) Distribucion pregunta 2
SELECT e.q2_conocimiento_despues AS opcion, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q2_conocimiento_despues
ORDER BY e.q2_conocimiento_despues;

-- 4) Distribucion pregunta 4
SELECT e.q4_sentimiento_general AS opcion, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q4_sentimiento_general
ORDER BY e.q4_sentimiento_general;

-- 5) Distribucion pregunta 6 (NPS)
SELECT e.q6_nps AS puntaje, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q6_nps
ORDER BY e.q6_nps;

-- 6) NPS consolidado (promotores, pasivos, detractores, score)
SELECT
  SUM(CASE WHEN e.q6_nps >= 9 THEN 1 ELSE 0 END) AS promotores,
  SUM(CASE WHEN e.q6_nps BETWEEN 7 AND 8 THEN 1 ELSE 0 END) AS pasivos,
  SUM(CASE WHEN e.q6_nps <= 6 THEN 1 ELSE 0 END) AS detractores,
  ROUND(
    (
      (SUM(CASE WHEN e.q6_nps >= 9 THEN 1 ELSE 0 END) - SUM(CASE WHEN e.q6_nps <= 6 THEN 1 ELSE 0 END))
      / NULLIF(COUNT(*), 0)
    ) * 100, 1
  ) AS nps_score
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta);

-- 7) Distribucion pregunta 7
SELECT e.q7_intencion_estudiar AS opcion, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q7_intencion_estudiar
ORDER BY e.q7_intencion_estudiar;

-- 8) Distribucion pregunta 9
SELECT e.q9_como_se_entero AS opcion, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q9_como_se_entero
ORDER BY e.q9_como_se_entero;

-- 9) Distribucion pregunta 10
SELECT e.q10_situacion_actual AS opcion, COUNT(*) AS total
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
GROUP BY e.q10_situacion_actual
ORDER BY e.q10_situacion_actual;

-- 10) Comentarios pregunta 8 (ultimos 100)
SELECT e.idencuesta, e.q8_que_cambiarias, e.dateinsert
FROM encuesta_wiener_fest e
WHERE e.estado = 'A'
  AND e.q8_que_cambiarias IS NOT NULL
  AND TRIM(e.q8_que_cambiarias) <> ''
  AND (@idcampania IS NULL OR e.idcampania = @idcampania)
  AND (@idevento IS NULL OR e.idevento = @idevento)
  AND (@desde IS NULL OR DATE(e.dateinsert) >= @desde)
  AND (@hasta IS NULL OR DATE(e.dateinsert) <= @hasta)
ORDER BY e.idencuesta DESC
LIMIT 100;
