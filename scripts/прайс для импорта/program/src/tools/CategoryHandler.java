package tools;

import java.util.ArrayList;
import java.util.Arrays;

import org.openqa.selenium.Rotatable;

public class CategoryHandler {
	private ArrayList<Category> catList;
	public CategoryHandler() {
		catList = new ArrayList<Category>();
		
	}
	/** добавляем в наш список 
	 * @param groupStr
	 * @return
	 */
	public boolean AddDirectory(String groupStr) {
		String[] arr = groupStr.split("\\\\");
		
		for (Category temp : catList) {
			if (temp.getCategory_name().equals(arr[0])) {
				return false;
			}
		}
		Category tempcat = new Category();
		tempcat.setCategory_name(arr[0]);
		tempcat.ParentCat = new ArrayList<String>(Arrays.asList(arr));
		tempcat.ParentCat.remove(0);
		return true;
	}
	
	
	
}
