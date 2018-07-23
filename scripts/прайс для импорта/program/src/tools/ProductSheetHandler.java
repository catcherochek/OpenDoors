package tools;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;

import java.util.ArrayList;
import java.util.Arrays;
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
	
	CategoryHandler cath;
	private final int RECORD_LENGTH = 26;
	private ArrayList<Goods> goodsList=null;
	
	public ArrayList<Goods> getGoodsList() {
		return goodsList;
	}
	private String fname;
	private XSSFWorkbook myWorkBook=null;
	private XSSFSheet productSheet;
	public  ProductSheetHandler(String fname) {
		super();
		goodsList = new ArrayList<Goods>();
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
        //XSSFSheet mySheet = myWorkBook.getSheet("products");
        
        // Get iterator to all the rows in current sheet
        this.productSheet = myWorkBook.getSheet("products");
            
       
        
        
	}
	//распарсивает лист в объекты
	@Override
	public ArrayList<Goods> ParseSheet() {
		
		
        ArrayList<Pair> template = new ArrayList<Pair>();
       template.add(new Pair("Имя", 0));
       
     	// Get iterator to all the rows in current sheet
       Iterator<Row> rowIterator = productSheet.iterator();
        while (rowIterator.hasNext()) {
        	Row row = rowIterator.next();
        	
            if (row.getRowNum()==0) {//пропускаем первую строку
            	continue;
            }//читаем из первой строки название и добавляем в массив
            Goods tempgoods = new Goods();
            tempgoods.name = row.getCell(0).getStringCellValue();
            tempgoods.description  = row.getCell(1).getStringCellValue();
            tempgoods.price =    row.getCell(2).toString();
            String[] temparray = row.getCell(3).getStringCellValue().split(",");
            tempgoods.photo = new ArrayList<String>(Arrays.asList(temparray));
            tempgoods.groupStr = row.getCell(4).getStringCellValue(); 
            tempgoods.typeStr = row.getCell(5).getStringCellValue(); 
            goodsList.add(tempgoods);
            
//                    Cell cell = cellIterator.next();
//                    Pair<Integer,Integer> temppair = new Pair<Integer, Integer>(1, cell.getColumnIndex());
//                    datarow.put(cell.getStringCellValue(),temppair);
               
//                
//            }
//            
        }
//        datarow.put("ImagePath", new Pair(21,5));
//        //создан массив с адресами полей, далее необходимо их заполнить
//        rowIterator = mySheet.iterator();
//        int goodscount = 0 ;
//        int goodslength = mySheet.getLastRowNum();
//        goodscount = (int)(goodslength/RECORD_LENGTH);
//        for (int i =0; i<goodscount; i++) {
//        	Goods tempdata = new Goods();
//        	int kol = (int)datarow.get("Product ID").getValue();
//        	int rownum =(int) i*RECORD_LENGTH; 
//        	tempdata.Goods_id = mySheet.getRow(rownum+1).getCell(datarow.get("Product ID").getValue()).getStringCellValue();
//        	tempdata.Name = mySheet.getRow(rownum+1).getCell(datarow.get("Name").getValue()).getStringCellValue();
//        	tempdata.Categories = mySheet.getRow(rownum+1).getCell(datarow.get("Categories").getValue()).getStringCellValue();
//        	gds.add(tempdata);
//        }
//        //TODO: сформирован список полей, формируем объекты категорий
//        mySheet = myWorkBook.getSheet("Meta");
//        rowIterator = mySheet.iterator();
//        this.cath = new CategoryHandler();
//        while (rowIterator.hasNext()) {
//        	Row row = rowIterator.next();
//        	if (row.getRowNum()<2) {
//        		rowIterator.next();
//        		continue;
//        	}
//        	String id = row.getCell(7).getStringCellValue();
//        	String name  = row.getCell(8).getStringCellValue();
//        	name = name.replaceAll("&nbsp;", "");
//        	String[] val = name.split("&gt;");
//        	cath.add(val, id);
//        	}
//        
        
        
		return goodsList;
		}

	
}
