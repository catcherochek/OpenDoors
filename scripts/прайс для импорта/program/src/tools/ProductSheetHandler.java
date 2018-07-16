package tools;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import org.apache.poi.ss.formula.functions.Address;
import org.apache.poi.ss.usermodel.Cell;
import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.ss.usermodel.Sheet;
import org.apache.poi.xssf.usermodel.XSSFSheet;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;

import javafx.util.Pair;

public class ProductSheetHandler extends ExcelHandler implements Handler {
	//конструктор листа типа products.xslsx
	//Pair<Integer, Integer> keyValue;
	//private int[] goods = new int[]();
	private HashMap<String,Pair<Integer, Integer>> datarow;
	CategoryHandler cath;
	private final int RECORD_LENGTH = 26;
	private ArrayList<Goods> gds=null;
	private String fname;
	private XSSFWorkbook myWorkBook=null;
	public  ProductSheetHandler(String fname) {
		super();
		gds = new ArrayList<Goods>();
		File myFile = new File(fname);
        FileInputStream fis;
		
		try {
			fis = new FileInputStream(myFile);
			try {
				myWorkBook = new XSSFWorkbook (fis);
			} catch (IOException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			}
		} catch (FileNotFoundException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		//End OfConstructor
		// Return first sheet from the XLSX workbook
        XSSFSheet mySheet = myWorkBook.getSheet("products");
        
        // Get iterator to all the rows in current sheet
        Iterator<Row> rowIterator = mySheet.iterator();
        
        // Traversing over each row of XLSX file
        while (rowIterator.hasNext()) {
            Row row = rowIterator.next();
            if (row.getRowNum()==0) {
            	row = rowIterator.next();}
            // For each row, iterate through each columns
            Iterator<Cell> cellIterator = row.cellIterator();
            while (cellIterator.hasNext()) {

                Cell cell = cellIterator.next();

                switch (cell.getCellType()) {
                case Cell.CELL_TYPE_STRING:
                    System.out.print(cell.getStringCellValue() + "\t");
                    break;
                case Cell.CELL_TYPE_NUMERIC:
                    System.out.print(cell.getNumericCellValue() + "\t");
                    break;
                case Cell.CELL_TYPE_BOOLEAN:
                    System.out.print(cell.getBooleanCellValue() + "\t");
                    break;
                default :
             
                }
            }
            System.out.println("");
            
        }
        
        
	}
	//распарсивает лист в объекты
	@Override
	public void ParseSheet() {
		// TODO Auto-generated method stub
		datarow = new HashMap<String,Pair<Integer, Integer>>();
		XSSFSheet mySheet = myWorkBook.getSheet("products");
        
        // Get iterator to all the rows in current sheet
        Iterator<Row> rowIterator = mySheet.iterator();
        while (rowIterator.hasNext()) {
        	Row row = rowIterator.next();
            if (row.getRowNum()==0) {//читаем из первой строки название и добавляем в массив
            	Iterator<Cell> cellIterator = row.cellIterator();
                while (cellIterator.hasNext()) {

                    Cell cell = cellIterator.next();
                    Pair<Integer,Integer> temppair = new Pair<Integer, Integer>(1, cell.getColumnIndex());
                    datarow.put(cell.getStringCellValue(),temppair);
                    }
                
            }
            
        }
        datarow.put("ImagePath", new Pair(21,5));
        //создан массив с адресами полей, далее необходимо их заполнить
        rowIterator = mySheet.iterator();
        int goodscount = 0 ;
        int goodslength = mySheet.getLastRowNum();
        goodscount = (int)(goodslength/RECORD_LENGTH);
        for (int i =0; i<goodscount; i++) {
        	Goods tempdata = new Goods();
        	int kol = (int)datarow.get("Product ID").getValue();
        	int rownum =(int) i*RECORD_LENGTH; 
        	tempdata.Goods_id = mySheet.getRow(rownum+1).getCell(datarow.get("Product ID").getValue()).getStringCellValue();
        	tempdata.Name = mySheet.getRow(rownum+1).getCell(datarow.get("Name").getValue()).getStringCellValue();
        	tempdata.Categories = mySheet.getRow(rownum+1).getCell(datarow.get("Categories").getValue()).getStringCellValue();
        	gds.add(tempdata);
        }
        //TODO: сформирован список полей, формируем объекты категорий
        mySheet = myWorkBook.getSheet("Meta");
        rowIterator = mySheet.iterator();
        this.cath = new CategoryHandler();
        while (rowIterator.hasNext()) {
        	Row row = rowIterator.next();
        	if (row.getRowNum()<2) {
        		rowIterator.next();
        		continue;
        	}
        	String id = row.getCell(7).getStringCellValue();
        	String name  = row.getCell(8).getStringCellValue();
        	name = name.replaceAll("&nbsp;", "");
        	String[] val = name.split("&gt;");
        	cath.add(val, id);
        	}
        
        
        
        
	}

	
}
