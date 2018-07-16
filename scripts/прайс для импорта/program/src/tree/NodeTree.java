package tree;

import java.util.ArrayList;
import java.util.Iterator;

public abstract class NodeTree<V> implements Iterable   {
	Iterator iter;
	private ArrayList<Node> tree;
	public NodeTree() {
		this.tree = new ArrayList<Node>();
	}
	//��������
	
	
	
	

	/** ��������� ������� � ������ �����
	 * @param n
	 */
	public void AddRootNode(Node n) {
		tree.add(n);
		n.setParent(new ArrayList<Node>());
	}
	
	/** ��������� ������� ����� ParentNode
	 * @param object
	 * @param ParentNode
	 * @throws Exception //� ������ ��������� �������� ParentNode  � ������
	 */
	public void AddNode(V v, ArrayList<Node> ParentNode ) throws Exception {
		
		for (Node d:ParentNode) {
			if (tree.indexOf(d)==-1) {//���� �������� ��� ������
				throw new Exception();
			}
			
		}
		Node n = new Node(v);
		n.setParent(ParentNode);
		this.tree.add(n);
		//Node n = new Node<V>(key, v);		
	}
	public void AddNode(Node n, Node ParentNode) throws Exception {
		if (tree.indexOf(n)!=-1) {//���� ������� ���� � ������
			throw new Exception();
		}
		if (tree.indexOf(ParentNode)==-1) {//���� �������� ��� ������
			throw new Exception();
		}
		n.addParent(ParentNode);
		this.tree.add(n);
	}
	/** ��������� ���� �� ������� � ������
	 * @param v 
	 * @param ParentNodes
	 * @param Childs
	 * @throws Exception 
	 */
	public void AddNodeAt(V v, ArrayList<Node> ParentNodes, ArrayList<Node> Childs) throws Exception {
		for (Node d:ParentNodes) {
			if (tree.indexOf(d)==-1) {//���� �������� ��� ������
				throw new Exception();
			}
			
		}
		for (Node d:Childs) {
			if (tree.indexOf(d)==-1) {//���� ����� ��� ������
				throw new Exception();
			}
			
		}
		Node n = new Node(v);
		n.setParent(ParentNodes);
		n.setChildren(Childs);
		this.tree.add(n);
		
	}
			
	
	/** ������� ���� � ��� ��������� � ��� ����� �� ������� ���������
	 * @param n
	 * @throws Exception
	 */
	public void DeleteNode(Node n) throws Exception {
		if (tree.indexOf(n)==-1) {//���� ���� ��� � ������
			throw new Exception();
		}
		ArrayList<Node> childs= n.getChildren();
		ArrayList<Node> parents = n.getParent();
		tree.remove(n);
		//������� ����� � ���������
		for (Node p:parents) {
			ArrayList<Node> p1 = p.getChildren();
			p1.remove(n);
			p1.addAll(childs);
			p.setChildren(p1);
		}
		for (Node ch:childs) {
			ArrayList<Node> ch1 = ch.getParent();
			ch1.remove(n);
			ch1.addAll(parents);
			ch.setParent(ch1);
			
		}
	}
		
	
	public void LinkNodes(Node n, Node nodesToLink) {
		
	}
	
	@Override
	public abstract  Iterator iterator();
}
