-- Custom Stocks Range
-- This report lets you view all orders within a specified time period.
-- You can click on a customer name to drill down into all orders for that customer.
-- VARIABLE: { 
--      name: "range", 
--      display: "Report Range",
--      type: "daterange", 
--      default: { start: "yesterday", end: "yesterday" }
-- }
-- FILTER: { 
--      column: "Symbol", 
--      filter: "drilldown",
--      params: {
--          macros: { "id": { column: "SCRIP Id" } },
--          report: "drilldown/customer-orders.sql"
--      }
-- }



SELECT 
	SCRIP.ID as 'SCRIP Id', 
	SYMBOL as 'Symbol', 
	SERIES as 'Series', 
	LAST as 'Last', 
	PREVCLOSE as 'Previous Close', 
	TOTTRDQTY as 'Volume ', 
	GAIN as 'Gain', 
	TIMESTAMP as 'Stock Date',
	stocks.CREATED_TIME as ' Created Time'
FROM 
	stocks.stocks INNER JOIN stocks.SCRIP ON SCRIP_ID = SCRIP.ID  
WHERE 
	TIMESTAMP BETWEEN "{{ range.start }}" AND "{{ range.end }}"
	AND GAIN > 6
ORDER BY SCRIP.ID 
