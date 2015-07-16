--  Details of the SCIRP
-- This is a drilldown report of the details of the SCRIP
-- VARIABLE: { name: "id", display: "SCRIP Id" }


SELECT
	ID as 'SCRIP_ID',
	SYMBOL as 'Symbol',
	SERIES as 'Series',
	CREATED_TIME as 'Created Time'
FROM
	stocks.SCRIP
WHERE 
	ID = "{{ id }}"
	