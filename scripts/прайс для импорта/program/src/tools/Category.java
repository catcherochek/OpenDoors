package tools;

import java.util.ArrayList;
import java.util.Arrays;

public class Category{
	
    //	Sort Order	Status	Design	Meta Title
	//60	        Двери	&lt;p&gt;Все возможные двери&lt;/p&gt;	Двери		0	0		doors	catalog/logo.png	Yes	1	0	Enabled	0:0	Двери
	//61	Двери Входные			двери, входные, металлические, деревянные	60	0				No	1	0	Enabled	0:0	двери входные 
	

	public int Cat_Level;
	//private int Category_id;
	
	
	
	public String Category_name;
	public String getCategory_name() {
		return Category_name;
	}

	public void setCategory_name(String category_name) {
		Category_name = category_name;
	}
	
	public ArrayList<String> ParentCat;

	public String Description;
	public ArrayList<String> Meta_Tag_Description;
	public ArrayList<String> Meta_Tag_Keywords;
	public Category ParentCategory;
	public ArrayList<String> Stores;
	public ArrayList<Filter> Filters;
	public ArrayList<String> SEO_Keyword;
	public String ImagePath;
	public boolean TopState;
	public int columns;
	public int Sort_Order;
	public String Status;
	public String Design;
	public String Meta_Title;
	public String id;
	
	public Category() {};
	
	public String GetTopState() {
		if(TopState) return "Yes";
		else return "No";
	}
	
	public void setTopState(String state) throws Exception {
		if (state == "Yes")
			TopState = true;
		else if (state == "No")
			TopState = false;
		else
			throw new Exception("Cannot set topstate in class Category cuz param "+state+" is incorrect ") ;
	}
		
	public void setMeta_Tag_Description(String meta_Tag_Description) {
		String[] arr = meta_Tag_Description.split(",");
		Meta_Tag_Description = new  ArrayList<String>(Arrays.asList(arr));
	}
	
	public void setMeta_Tag_Keywords(String meta_Tag_Keywords) {
		String[] arr = meta_Tag_Keywords.split(",");
		Meta_Tag_Keywords = new  ArrayList<String>(Arrays.asList(arr));
		//Meta_Tag_Keywords = meta_Tag_Keywords;
	}
		
	public void setStores(String stores) {
	//	Arrays.as
		String[] arr = stores.split(",");
		Stores = new  ArrayList<String>(Arrays.asList(arr));
		
	}
	
	public void setFilters(ArrayList<Filter> filters) {
		//TODO:реализовать парсинг со строки
		
		Filters = filters;
	}
	
	public void setSEO_Keyword(String sEO_Keyword) {
		String[] arr = sEO_Keyword.split(",");
		SEO_Keyword = new  ArrayList<String>(Arrays.asList(arr));
		//SEO_Keyword = sEO_Keyword;
	}



	
	
	
	
}



