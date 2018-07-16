package tree;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class Node<V> {
	//private String Key;
	private V Value;
	//private int level;//уровень
	private ArrayList<Node> parent;
	private ArrayList<Node> children;
	
	public Node(V value) {
		//this.Key = key;
		this.Value = value;
		children = new ArrayList<Node>();
		parent = new ArrayList<Node>();
		
	}

	

	public V getValue() {
		return Value;
	}

	public void setValue(V value) {
		Value = value;
	}

	

	public ArrayList<Node> getParent() {
		return parent;
	}

	public void addParent(Node parent) {
		this.parent.add(parent);
	}
	public void setParent(ArrayList<Node> parent) {
		this.parent = parent;
	}

	public ArrayList<Node> getChildren() {
		return children;
	}

	public void setChildren(ArrayList<Node> children) {
		this.children = children;
	}
	public void AddChild(Node child) {
		this.children.add(child);
	}

	public boolean hasChilds() {
		
		if (this.getChildren().size()==0) {
		return false;}
		return true;
	}

	public boolean hasParents() {
		if (this.getParent().size()==0) {
			return false;
		}
		// TODO Auto-generated method stub
		return true;
	}

	


	
	
	
	
}
