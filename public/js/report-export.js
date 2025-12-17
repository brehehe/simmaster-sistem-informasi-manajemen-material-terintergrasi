/**
 * SIMMASTER Report Export Utilities
 * Client-side Excel and PDF export for reports
 */

// Export table to Excel using SheetJS with auto-width columns
function exportToExcel(tableId, filename = 'report') {
    try {
        // Get table element
        const table = document.getElementById(tableId);
        if (!table) {
            console.error('Table not found:', tableId);
            return;
        }

        // Create workbook from table
        const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

        // Get the worksheet
        const ws = wb.Sheets["Sheet1"];

        // Calculate column widths based on content
        const range = XLSX.utils.decode_range(ws['!ref']);
        const colWidths = [];

        for (let C = range.s.c; C <= range.e.c; ++C) {
            let maxWidth = 10; // minimum width

            for (let R = range.s.r; R <= range.e.r; ++R) {
                const cellAddress = XLSX.utils.encode_cell({ r: R, c: C });
                const cell = ws[cellAddress];

                if (cell && cell.v) {
                    const cellValue = cell.v.toString();
                    const cellWidth = cellValue.length;
                    maxWidth = Math.max(maxWidth, cellWidth);
                }
            }

            // Add some padding and set reasonable limits
            colWidths.push({ wch: Math.min(maxWidth + 2, 50) });
        }

        // Apply column widths
        ws['!cols'] = colWidths;

        // Generate filename with timestamp
        const timestamp = new Date().toISOString().slice(0, 10);
        const fullFilename = `${filename}_${timestamp}.xlsx`;

        // Write file
        XLSX.writeFile(wb, fullFilename);

        console.log('Excel exported successfully:', fullFilename);
    } catch (error) {
        console.error('Error exporting to Excel:', error);
        alert('Gagal export Excel. Silakan coba lagi.');
    }
}

// Export table to PDF using jsPDF and AutoTable
function exportToPDF(options = {}) {
    try {
        const {
            tableId = 'reportTable',
            title = 'Laporan',
            filename = 'report',
            orientation = 'landscape',
            summaryCards = null
        } = options;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF(orientation, 'mm', 'a4');

        // Add header
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.text(title, 14, 15);

        // Add date
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        const today = new Date().toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        doc.text(`Tanggal: ${today}`, 14, 22);

        // Add summary if provided
        let startY = 28;
        if (summaryCards && summaryCards.length > 0) {
            doc.setFontSize(9);
            summaryCards.forEach((card, index) => {
                const xPos = 14 + (index * 70);
                doc.text(`${card.label}: ${card.value}`, xPos, startY);
            });
            startY += 7;
        }

        // Get table element
        const table = document.getElementById(tableId);
        if (!table) {
            console.error('Table not found:', tableId);
            return;
        }

        // Extract headers (skip No column if needed, keep all data)
        const headers = [];
        const headerCells = table.querySelectorAll('thead tr th');
        headerCells.forEach(th => {
            headers.push(th.textContent.trim());
        });

        // Extract rows
        const rows = [];
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(tr => {
            const row = [];
            const cells = tr.querySelectorAll('td');
            cells.forEach(td => {
                // Get text content, clean up whitespace
                let text = td.innerText || td.textContent;
                text = text.trim().replace(/\s+/g, ' ');
                row.push(text);
            });
            if (row.length > 0) {
                rows.push(row);
            }
        });

        // Generate table with auto column widths
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: startY,
            styles: {
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak',
                cellWidth: 'auto'
            },
            headStyles: {
                fillColor: [59, 130, 246], // Blue color
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center'
            },
            alternateRowStyles: {
                fillColor: [245, 247, 250]
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 10 }, // No column
            },
            margin: { top: 10, left: 10, right: 10 },
            tableWidth: 'auto',
            didDrawPage: function (data) {
                // Footer
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                doc.text(
                    `Halaman ${data.pageNumber} dari ${pageCount}`,
                    doc.internal.pageSize.width / 2,
                    doc.internal.pageSize.height - 10,
                    { align: 'center' }
                );
            }
        });

        // Generate filename with timestamp
        const timestamp = new Date().toISOString().slice(0, 10);
        const fullFilename = `${filename}_${timestamp}.pdf`;

        // Save PDF
        doc.save(fullFilename);

        console.log('PDF exported successfully:', fullFilename);
    } catch (error) {
        console.error('Error exporting to PDF:', error);
        alert('Gagal export PDF. Silakan coba lagi.');
    }
}

// Helper function to format number for display
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Helper function to format currency for display
function formatCurrency(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num);
}
