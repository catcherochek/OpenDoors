package libs;

import org.eclipse.swt.widgets.Display;
import org.eclipse.swt.widgets.Shell;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.Text;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

import tools.DialogHandler;
import tools.Goods;
import tools.OCTemplateSaver;
import tools.ProductSheetHandler;
import tools.WebHandler;
import tree.IterableTree;
import tree.Node;

//import com.sun.java.util.jar.pack.Package.File;

import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.widgets.FileDialog;

//import java.awt.FileDialog;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.logging.ConsoleHandler;

import org.eclipse.swt.widgets.Table;
import org.eclipse.wb.swt.SWTResourceManager;


import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.Iterator;

import org.apache.poi.EncryptedDocumentException;
import org.apache.poi.openxml4j.exceptions.InvalidFormatException;
import org.apache.poi.sl.usermodel.Sheet;
import org.apache.poi.ss.usermodel.Cell;
import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.ss.usermodel.Workbook;
import org.apache.poi.ss.usermodel.WorkbookFactory;
import org.apache.poi.xssf.usermodel.XSSFSheet;
//import org.apache.poi.EncryptedDocumentException;
//
////
//
//import org.apache.poi.openxml4j.exceptions.InvalidFormatException;
//import org.apache.poi.ss.usermodel.*;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;

public class main {
	private static final String FILE_NAME = "/tmp/MyFirstExcel.xlsx";
	protected Shell shell;
	private Text txtHelloworld;
	String PROJECT_ROOT_PATH = System.getProperty("user.dir");
	/**
	 * Launch the application.
	 * @param args
	 */
	public static void main(String[] args) {
		try {
			main window = new main();
			window.open();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * Open the window.
	 */
	public void open() {
		Display display = Display.getDefault();
		createContents();
		shell.open();
		shell.layout();
		while (!shell.isDisposed()) {
			if (!display.readAndDispatch()) {
				display.sleep();
			}
		}
	}

	/**
	 * Create contents of the window.
	 */
	protected void createContents() {
		shell = new Shell();
		shell.setSize(800, 662);
		shell.setText("SWT Application");
		
		txtHelloworld = new Text(shell, SWT.BORDER | SWT.V_SCROLL);
		txtHelloworld.setBackground(SWTResourceManager.getColor(SWT.COLOR_LIST_FOREGROUND));
		txtHelloworld.setForeground(SWTResourceManager.getColor(SWT.COLOR_WHITE));
		txtHelloworld.setEditable(false);
		txtHelloworld.setText("Console here");
		txtHelloworld.setBounds(10, 10, 764, 184);
		
		tools.ConsoleHandler ch = new tools.ConsoleHandler(txtHelloworld);
		
		Button btnNewButton = new Button(shell, SWT.NONE);
		btnNewButton.addSelectionListener(new SelectionAdapter() {
			@Override
			public void widgetSelected(SelectionEvent e) {
/*				txtHelloworld.setText("helloworld");
				//System.setProperty("webdriver.gecko.driver", PROJECT_ROOT_PATH+"\\lib\\gecko\\geckodriver.exe");
				//WebDriver driver = new FirefoxDriver();
				//driver.get("http://www.google.com");
				*/ 
				
			   /*
			   // String str = "C:\\wamp\\www\\oc2\\scripts\\����� ��� �������\\���������\\������� �������\\products\\products_excelport_light_ru-ru_localhost_oc2_2018-06-05_20-56-00_0.xlsx";
			    System.out.println("Save to: "+dh.getOut());/*
		      // C:\wamp\www\oc2\scripts\����� ��� �������\���������\������� �������\products\products_excelport_full_en-gb_localhost_oc2_2018-06-19_13-52-09_0.xlsx
			    ch.Log("sdfsdsg", tools.ConsoleHandler.CONSOLEHANDLER_LOGTYPE_MESSAGE);
			    ch.Log("sdfsdsg", tools.ConsoleHandler.CONSOLEHANDLER_LOGTYPE_WARNING);
			    IterableTree<String> itt= new IterableTree<String>();
			    Node<String> mainn =new Node<String>("main");
			    itt.AddRootNode(mainn);
			    try {
			    	Node<String> aftermain1 = new Node<String>("aftermain1");
			    	Node<String> aftermain2 = new Node<String>("aftermain2");
			    	Node<String> aftermain3 = new Node<String>("aftermain3");
					itt.AddNode(aftermain1, mainn);
					itt.AddNode(aftermain2, mainn);
					itt.AddNode(aftermain3, mainn);
					itt.AddNode("afteraftermain1",new ArrayList<Node>(Arrays.asList(aftermain1,aftermain2))  );
					
				} catch (Exception e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
			    itt.SetIterator("asc", itt.getNode("afteraftermain1"));
			    Iterator<Node> it = itt.iterator();
			    while(it.hasNext()) {
			    	Node n = it.next();
			    	System.out.print(n.getValue());
			    }
			    ProductSheetHandler ps = new ProductSheetHandler(str);
	            ps.ParseSheet();
	            System.out.println("Save to: "+out[0]);
			   	
			*/ 
				
				 DialogHandler dh = new DialogHandler(new String[] { "*.xlsx", "*.*" },
		    									new String[] { "Excel xslsx files", "All Files (*.*)" }, 
			    									PROJECT_ROOT_PATH, 
			    									shell);
			    //�вапвыапвы
				 ProductSheetHandler ps = new ProductSheetHandler(dh.getOut());
				 //�������� ������ ������� �� ����� �����, ����� ������������ ����������
				 ps.ParseSheet();
				 //� �������� � ���������� ��� ������ � ���
				 
				 WebHandler wh = new WebHandler();
				 
				 //������� ������, ����������� � ��������� ���� ��� �������
				 OCTemplateSaver ts = new OCTemplateSaver(dh.getOut()+"_out");
				 //��������� ������ ������� � ����
				 //ts.SaveTo(goodsList);
				 
				
				
				
				
				
			  }
		       
		    
			
		});
		btnNewButton.setBounds(10, 214, 89, 25);
		btnNewButton.setText("открыть файл");

	}
}
